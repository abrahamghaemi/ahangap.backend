<?php

namespace Espo\Custom\Controllers;

class Album extends \Espo\Core\Templates\Controllers\Base
{
    public function actionPopular($params, $data, $request)
    {
        return 'ok';
    }

    public function actionNewest($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);
        // where[0][type]=lastXDays&where[0][attribute]=publishedDate&where[0][value]=30
        $where = [[
            "type" => "lastXDays",
            "attribute" => "published",
            "value" => 30
        ]];
        $params['where'] = $where;

        return $this->getListOfTrack($params);
    }
}
