<?php
/**
 * Controller which handles the (requested) pdc-item(s).
 */

namespace OWC\PDC\Locations\RestAPI\Controllers;

use OWC\PDC\Locations\Models\Location;
use OWC\PDC\Base\RestAPI\Controllers\BaseController;
use WP_Error;
use WP_REST_Request;

/**
 * Controller which handles the (requested) pdc-item(s).
 */
class LocationsController extends BaseController
{

    /**
     * Get a list of all items.
     *
     * @param WP_REST_Request $request
     *
     * @return array
     */
    public function getItems(WP_REST_Request $request)
    {
        $locations = new Location($this->plugin);

        $data = $locations->all();
        $query = $locations->getQuery();

        return $this->addPaginator($data, $query);
    }

    /**
     * Get an individual post item.
     *
     * @param $request $request
     *
     * @return array|WP_Error
     */
    public function getItem(WP_REST_Request $request)
    {
        $id = (int) $request->get_param('id');

        $location = (new Location)
            ->find($id);

        if (! $location) {
            return new WP_Error('no_item_found', sprintf('Item with ID "%d" not found', $id), [
                'status' => 404
            ]);
        }

        return $location;
    }
}
