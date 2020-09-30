<?php

namespace BIWS\TaKiEventManager\views\rest;

use BIWS\CPTBuilder\models\fields\FieldType;
use BIWS\CPTBuilder\models\fields\IField;
use BIWS\CPTBuilder\models\MetaBox;
use BIWS\CPTBuilder\models\Taxonomy;
use BIWS\CPTBuilder\views\IViewController;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_Term;
use WP_Term_Query;

abstract class AbstractRestEndpointViewController implements IViewController
{
    private RestProps $props;

    public function __construct(RestProps $props)
    {
        $this->props = $props;
    }

    public function init(): void
    {
    }

    public function remove(): void
    {
    }

    public function registerWithCallback(callable $callback)
    {
        $args = $this->props->getArgs();
        $args['callback'] = $callback;
        register_rest_route(
            $this->props->getNamespace(),
            $this->props->getRoute(),
            $args,
            $this->props->isOverride()
        );
    }

    /**
     * @return WP_REST_Response|WP_Error 
     */
    public function getRestResponse(WP_REST_Request $request)
    {
        $query = $this->prepareQuery($request);
        $data = $this->collectData($query);
        wp_reset_postdata();
        return $this->prepareResponse($query, $data);
    }

    /**
     * Prepares query for data collection
     * 
     * Allows to prepare the query based on the classes properties and the given
     * $request data.
     * 
     * @param WP_REST_Request $request The passed in request data.
     *
     * @return WP_Query|WP_Term_Query|null The query to be processed or null
     *                                     if the query could not be created.
     */
    protected abstract function prepareQuery(WP_REST_Request $request);

    /**
     * Collects response data
     * 
     * Collects data based on the given $query.
     * @param WP_Query|WP_Term_Query|null $query The query to process
     *
     * @return array|null|WP_Error The collected data,
     *                             null if no data could be collected
     *                             or WP_Error if an error occurred
     */
    protected abstract function collectData($query);

    /**
     * Prepares rest response for frontend
     *
     * Prepares the rest response used by the view and handles errors
     * accordingly.
     *
     * @param WP_Query|WP_Term_Query|null $query The $query used to query the data.
     * @param array|null|WP_Error         $data  The collected data or
     *                                           null|WP_Error if there was an error.
     * @return WP_REST_Response|WP_Error WP_Rest_response on success,
     *                                   WP_Error otherwise
     *
     * @see this::collectData()
     */
    protected abstract function prepareResponse($query, $data);


    /**
     * 
     * Terms/Taxonomies
     * 
     */

    /**
     * Collects term data for referenced Taxonomy
     *
     * Collects term data for the rest response based on the referenced
     * $taxonmy.
     *
     * @param Taxonomy $taxonomy The Taxonomy the given terms are referenced to.
     * @param WP_Term[] $terms Queried array of WP_Term objects
     *
     * @return array|WP_Error An array with the collected $terms data or
     *                        WP_Error if an error occurred,
     */
    protected function collectTaxonomyTermData(Taxonomy $taxonomy, array $terms)
    {
        $data = array();

        foreach ($terms as $term) {
            $term_data = $this->collectTermData($taxonomy, $term);
            if ($term_data instanceof WP_Error) {
                return $term_data;
            }
            if (!empty($term_data)) {
                $data[] = $term_data;
            }
        }

        return $data;
    }


    /**
     * Collects term data for a single $term
     *
     * Collects term data for the rest response based on the referenced
     * $taxonmy.
     *
     * @param Taxonomy $taxonomy The Taxonomy the given terms are referenced to.
     * @param WP_Term $term Queried WP_Term object
     *
     * @return array|WP_Error An array with the collected $term data or
     *                        WP_Error if an error occurred,
     */
    private function collectTermData(Taxonomy $taxonomy, WP_Term $term)
    {
        if ($term->taxonomy !== $taxonomy->getId()) {
            return new WP_Error(
                'not_referenced_term',
                'The given term does not match the provided taxonomy. '
                    . "Term = [id:{$term->term_id}, slug:{$term->slug}], "
                    . "Taxonomy = [slug:{$taxonomy->getId()}]",
                array('status' => 400)
            );
        }

        $term_data = array(
            'name' => $term->name,
            'slug' => $term->slug,
        );

        $description = $term->description;
        if ($description) {
            $term_data['description'] = $description;
        }

        foreach ($taxonomy->getFields() as $field) {
            $value = $this->getTermFieldValue($term->term_taxonomy_id, $field);
            if ($field->getType() === FieldType::CHECKBOX) {
                $term_data[$field->getId()] = $value ? true : false;
            } else if ($value) {
                $term_data[$field->getId()] = $value;
            }
        }

        return $term_data;
    }

    /**
     * Queries term field value
     * 
     * Queries the term metadata for the given $term_id and $field
     * @param IField $field The term field to query
     * @param int $term_id The term id for which to query the field
     * @return mixed based on $field->type FieldType
     * 
     * @see https://developer.wordpress.org/reference/functions/get_term_meta/
     */
    private function getTermFieldValue(int $term_id, IField $field)
    {
        return get_term_meta($term_id, $field->getId(), true);
    }

    /**
     * 
     * META BOXES
     * 
     */

    /**
     * Collects post meta
     *
     * Collects post meta for the rest response based on the referenced
     * $meta_box.
     *
     * @param MetaBox $meta_box The MetaBox for which to collect the data.
     * @param int $post_id the post ID for which to query the meta data.
     *
     * @return array An array with the collected post meta data
     */
    protected function collectMetaBoxData(
        MetaBox $meta_box,
        int $post_id
    ): array {
        $meta_data = array();

        foreach ($meta_box->getFields() as $field) {
            $value = $this->getMetaFieldValue($post_id, $field);
            if ($field->getType() === FieldType::CHECKBOX) {
                $meta_data[$field->getId()] = $value ? true : false;
            } else if ($value) {
                $meta_data[$field->getId()] = $value;
            }
        }

        return $meta_data;
    }

    /**
     * Queries term field value
     * 
     * Queries the term metadata for the given $term_id and $field
     * @param IField $field The term field to query
     * @param int $post_id The post id for which to query the field
     * @return mixed based on $field->type FieldType
     * 
     * @see https://developer.wordpress.org/reference/functions/get_post_meta/
     */
    private function getMetaFieldValue(int $post_id, IField $field)
    {
        return get_post_meta($post_id, $field->getId(), true);
    }
}
