<?php

namespace Drupal\drug_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SearchController.
 */
class SearchController extends ControllerBase
{

    /**
     * Search.
     *
     * @return string
     *   Return Hello string.
     */
    public function search()
    {
        $get = $_GET;
        $url = 'http://pubmed.ncbi.nlm.nih.gov/?SUBMIT=y&db=' . $_GET['db'] . '&term=' . $_GET['term'] . '&dispmax=' . $_GET['dispmax'] . '&relentrezdate=' . $_GET['relentrezdate'];
        return [
            '#theme' => 'drug_search_search',
            '#url' => $url,
            '#get' => $get
        ];
    }

}
