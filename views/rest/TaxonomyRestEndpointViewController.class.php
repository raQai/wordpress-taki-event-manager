<?php

namespace BIWS\TaKiEventManager\views\rest;

use BIWS\CPTBuilder\models\Taxonomy;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_Term_Query;

class TaxonomyRestEndpointViewController extends AbstractRestEndpointViewController
{
    private Taxonomy $taxonomy;

    public function __construct(Taxonomy $taxonomy, RestProps $props)
    {
        parent::__construct($props);
        $this->taxonomy = $taxonomy;
    }

    /**
     * Prepares query for data collection
     * 
     * Allows to prepare the query based on the classes properties and the given
     * $request data.
     * 
     * @param WP_REST_Request $request The passed in request data.
     *
     * @return WP_Term_Query|null The query to be processed or null
     *                                     if the query could not be created.
     */
    protected function prepareQuery(WP_REST_Request $request): ?WP_Term_Query
    {
        return new WP_Term_Query(array(
            'taxonomy' => $this->taxonomy->getId(),
            'hide_empty' => false,
        ));
    }

    /**
     * Collects response data
     * 
     * Collects data based on the given $query.
     * @param WP_Term_Query|null $query The query to process
     *
     * @return array|null|WP_Error The collected data,
     *                             null if no data could be collected
     *                             or WP_Error if an error occurred
     */
    protected function collectData($query)
    {
        if (!($query instanceof WP_Term_Query)) {
            return $query instanceof WP_Error ? $query : null;
        }

        return $this->collectTaxonomyTermData(
            $this->taxonomy,
            $query->get_terms()
        );
    }

    /**
     * Prepares rest response for frontend
     *
     * Prepares the rest response used by the view and handles errors
     * accordingly.
     *
     * @param WP_Term_Query|null  $query The $query used to query the data.
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

        if ($data instanceof WP_Error) {
            return $data;
        }

        if ($data === null) {
            return new WP_Error(
                'null_data',
                'Query did not process correctly',
                array('status' => 400)
            );
        }

        if (empty($data)) {
            return new WP_Error(
                "no_{$this->taxonomy->getId()}_data",
                "No data found for taxonomy {$this->taxonomy->getId()}",
                array('status' => 404)
            );
        }

        return new WP_REST_Response($data);
    }
}
