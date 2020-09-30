<?php

namespace BIWS\TaKiEventManager\views\rest;

use BIWS\CPTBuilder\models\CustomPostType;
use DateTime;
use DateTimeZone;
use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

class CPTRestEndpointViewController extends AbstractRestEndpointViewController
{
    private CustomPostType $cpt;

    /**
     * @param int $posts_per_page derived by request params['posts_per_page']
     *
     * @see this::prepareQuery()
     */
    private int $posts_per_page = -1;

    /**
     * @param int $paged derived by request params['paged']
     *
     * @see this::prepareQuery()
     */
    private int $paged = -1;

    public function __construct(CustomPostType $cpt, RestProps $props)
    {
        parent::__construct($props);
        $this->cpt = $cpt;
    }

    /**
     * Prepares query for data collection
     * 
     * Allows to prepare the query based on the classes properties and the given
     * $request data.
     * 
     * @param WP_REST_Request $request The passed in request data.
     *
     * @return WP_Query|null The query to be processed or null
     *                       if the query could not be created.
     */
    protected function prepareQuery(WP_REST_Request $request): ?WP_Query
    {
        $params = $request->get_params();
        $params['meta_query'] = $this->getMetaQueryParams();
        $params['post_type'] = $this->cpt->getSlug();
        // avoid pagination due to sorting postprocessing
        $params['nopaging'] = true;

        // store pagination params for post processing
        if (isset($params['posts_per_page'])) {
            $this->posts_per_page = intval($params['posts_per_page']);
        }

        if (isset($params['paged'])) {
            $this->paged = intval($params['paged']);
        }

        return new WP_Query($params);
    }

    /**
     * Collects response data
     * 
     * Collects data based on the given $query.
     * @param WP_Query|null $query The query to process
     *
     * @return array|null|WP_Error The collected data,
     *                             null if no data could be collected
     *                             or WP_Error if an error occurred
     */
    protected function collectData($query)
    {
        if (!($query instanceof WP_Query)) {
            return null;
        }

        $data = [];
        while ($query->have_posts()) {
            $event = $query->the_post();
            $event_id = get_the_ID();
            $event_data = array(
                'title' => wp_kses_post(get_the_title()),
                'link' =>  get_the_permalink(),
                'content' => wp_kses_post(get_the_content()),
                'slug' => get_post_field('post_name', $event),
                'status' => get_post_status($event_id),
            );
            foreach ($this->cpt->getTaxonomies() as $taxonomy) {
                $terms = get_the_terms($event_id, $taxonomy->getId());
                if ($terms instanceof WP_Error) {
                    return $terms;
                }
                if (empty($terms)) {
                    continue;
                }
                $taxonomy_data = $this->collectTaxonomyTermData(
                    $taxonomy,
                    $terms
                );
                if ($taxonomy_data instanceof WP_Error) {
                    return $taxonomy_data;
                }
                if (empty($taxonomy_data)) {
                    continue;
                }
                $event_data[$taxonomy->getId()] = $taxonomy_data;
            }
            foreach ($this->cpt->getMetaBoxes() as $meta_box) {
                $meta_box_data = $this->collectMetaBoxData(
                    $meta_box,
                    $event_id
                );

                if (empty($meta_box_data)) {
                    continue;
                }
                $event_data[$meta_box->getId()] = $meta_box_data;
            }

            $data[] = $event_data;
        }

        return $data;
    }

    /**
     * Prepares rest response for frontend
     *
     * Prepares the rest response used by the view and handles errors
     * accordingly.
     *
     * @param WP_Query|null       $query The $query used to query the data.
     * @param array|null|WP_Error $data  The collected data or
     *                                   null|WP_Error if there was an error.
     * @return WP_REST_Response|WP_Error WP_Rest_response on success,
     *                                   WP_Error otherwise
     *
     * @see this::collectData()
     */
    protected function prepareResponse($query, $data)
    {
        if ($query === null) {
            return new WP_Error(
                'bad_query',
                'Query could not be build.',
                array('status' => 400)
            );
        }

        if ($data === null) {
            return new WP_Error(
                'null_data',
                'Query did not process correctly',
                array('status' => 400)
            );
        }

        if ($data instanceof WP_Error) {
            return $data;
        }

        $data = $this->postProcessData($data);

        if (empty($data)) {
            return new WP_Error(
                "no_{$this->cpt->getSlug()}_data",
                "No data found for post type {$this->cpt->getSlug()}",
                array('status' => 404)
            );
        }

        $response = new WP_REST_Response($data);

        $numPages = $this->numPages($query);
        $response->header('X-WP-TotalPages', $numPages);
        $response->header('X-WP-Total', $this->numPosts($query, $numPages));

        return $response;
    }

    /**
     * Post processes the collected $data
     * 
     * Post processes the collected $data by sorting it with the provided
     * sorting method this::compare() and slicing the array data to only display
     * the based on the pagination settings specified posts.
     * 
     * @param array $data The collected data
     *
     * @return array The post processed data
     *
     * @see this::collectData()
     * @see this::compare()
     */
    private function postProcessData(array $data): array
    {
        // sort data
        usort($data, array($this, 'compare'));

        $posts_per_page = $this->posts_per_page;
        // return full data if no 'posts_per_page' specified
        if ($posts_per_page <= 0) {
            return $data;
        }

        $paged = $this->paged;
        // return first page data if no 'paged' specified
        if ($paged <= 0) {
            return array_slice($data, 0, $posts_per_page);
        }

        // return specified page data
        return array_slice(
            $data,
            ($paged - 1) * $posts_per_page,
            $posts_per_page
        );
    }

    /**
     * Helper method to calculate the num pages after post processing the $data
     *
     * @param WP_Query $query The query used to collect the data.
     * @return int 1 if posts_per_page was <= 0 indicating no pagination,
     *             the calculated number of pages otherwise.
     */
    private function numPages(WP_Query $query): int
    {
        $posts_per_page = $this->posts_per_page;
        if ($posts_per_page <= 0) {
            return 1;
        }

        return ceil($query->found_posts / $posts_per_page);
    }

    /**
     * Helper method to calculate the num posts after post processing the $data
     *
     * @param WP_Query $query    The query used to collect the data.
     * @param int      $numPages The post processed num pages
     *
     * @return int 1 if posts_per_page was <= 0 indicating no pagination,
     *             the calculated number of pages otherwise.
     *
     * @see this::numPages()
     */
    private function numPosts(WP_Query $query, int $numPages)
    {
        $post_count = $query->found_posts;
        $posts_per_page = $this->posts_per_page;
        if ($posts_per_page <= 0) {
            return $post_count;
        }

        $paged = $this->paged;
        if ($paged < $numPages) {
            return min($post_count, $posts_per_page);
        }
        return $post_count % $posts_per_page;
    }

    /**
     * Builds the meta_query parameters for the post query
     * 
     * @return array to be used as the querys 'meta_query' parameter value
     */
    private function getMetaQueryParams(): array
    {
        $date = new DateTime('now', new DateTimeZone('Europe/Berlin'));
        $today_date = $date->format('Y-m-d');
        $now_time = $date->format('H:m');
        return array(
            'relation' => 'OR',
            array(
                'key' => 'datetime__start_date',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key' => 'datetime__start_date',
                'value' => '',
                'compare' => '=',
            ),
            array(
                'key' => 'datetime__start_date',
                'value' => $today_date,
                'type' => 'DATE',
                'compare' => '>',
            ),
            array(
                'key' => 'datetime__end_date',
                'value' => $today_date,
                'type' => 'DATE',
                'compare' => '>',
            ),
            array(
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'datetime__start_date',
                        'value' => $today_date,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'datetime__end_date',
                        'value' => $today_date,
                        'compare' => '=',
                    ),
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'datetime__start_time',
                        'value' => '',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'datetime__start_time',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key' => 'datetime__end_time',
                        'value' => '',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'datetime__end_time',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key' => 'datetime__start_time',
                        'value' => $now_time,
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'datetime__end_time',
                        'value' => $now_time,
                        'compare' => '>=',
                    ),
                )
            ),
        );
    }

    /**
     * Compares two posts
     * 
     * Compares two posts $event and $other by set meta dates and times. The
     * order is determined as follows:
     * 
     * - if the start date does not exist, add this to the top
     * - if the start date exists, first order by start date
     * - if the start time exists, second order by start time
     * - if the end time exists, third order by end time
     * - if the end date exists, last order by end date
     *
     * @return int 0 if $event and $other have the same order
     *             -1 if $event should be before $other
     *             1 if $event should be after $other
     */
    public function compare($event, $other): int
    {
        $event_meta = $event['biws__datetime_meta'];
        $other_meta = $other['biws__datetime_meta'];
        $compare_keys = array(
            'datetime__start_date',
            'datetime__start_time',
            'datetime__end_time',
            'datetime__end_date'
        );

        foreach ($compare_keys as $key) {
            $key_exists = array_key_exists($key, $event_meta);
            $other_exists = array_key_exists($key, $other_meta);
            $compare = 0;
            if (!$key_exists && !$other_exists) {
                continue;
            } else if (!$key_exists) {
                $compare = -1;
            } else if (!$other_exists) {
                $compare = 1;
            } else {
                $compare = $event_meta[$key] <=> $other_meta[$key];
            }
            if ($compare !== 0) {
                return $compare;
            }
        }

        return 0;
    }
}
