<?php
/**
 * Model for the item
 */

namespace OWC\PDC\Locations\Models;

use OWC\PDC\Base\Models\Model;
use \WP_Post;

/**
 * Model for the item
 */
class Location extends Model
{

    /**
     * Type of model.
     *
     * @var string $posttype
     */
	protected $posttype = 'pdc-location';

	/**
	 * All meta data of post
	 *
	 * @var array
	 */
	protected $allPostMeta = [];

    /**
     * Container with fields, associated with this model.
     *
     * @var array $globalFields
     */
    protected static $globalFields = [];

    /**
     * Transform a single WP_Post item.
     *
     * @param WP_Post $post
     *
     * @return array
     */
    public function transform(WP_Post $post, $metaboxes)
    {
		$this->allPostMeta = $this->getAllPostMeta($post);
		$fields = $this->getFields($metaboxes['locations']['fields']);
		$data = [
            'title'   => $post->post_title,
            'date'    => $post->post_date
        ];

        $data = $this->assignFields(array_merge($data, $fields), $post);

        return $data;
	}

	/**
	 * Get all meta assigned to post
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	protected function getAllPostMeta($post)
	{
		return get_metadata('post', $post->ID);
	}

    /**
     * Filter fields from config.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getFields($fields): array
    {
		$fields = $this->removeUnnecessaryFieldsByKey($fields);
		$fields = array_map(function($field) use ($fields) {
			return $this->manipulate($field, $key);
		},
			$fields
		);

		return $fields;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $fields
	 * @return void
	 */
	protected function removeUnnecessaryFieldsByKey( $fields )
	{

		$toRemove = ['divider'];
		$fields = array_filter( $fields, function($key) use ($toRemove) {
			return (!in_array($key, $toRemove));
		}, ARRAY_FILTER_USE_KEY);

		return $fields;
	}

	/**
	 * Undocumented function
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function manipulate ($fields)
	{
		if ( count( $fields ) < 1 ) {
			return $fields;
		}

		foreach( $fields as $key => $field ) {
			if (!array_key_exists('_owc_' . $field['id'], $this->allPostMeta) AND ( !in_array('_owc_' . $field['id'], $this->allPostMeta ) )) {
				unset($fields[$key]);
				continue;
			}

			$fieldName = str_replace($this->posttype .'-', '', $field['id']);
			$content = $this->allPostMeta['_owc_' . $field['id']][0] ?? '';
			$fields[$fieldName] = maybe_unserialize($content);
			unset($fields[$key]);
		}

		return $fields;
	}
}
