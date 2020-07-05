<?php


namespace Espo\Core\Utils;

class ControllerUtil
{
    public static function fetchListParamsFromRequest(&$params, $request, $data)
    {
        $params['where'] = $request->get('where');
        $params['maxSize'] = $request->get('maxSize');
        $params['offset'] = $request->get('offset');

        if ($request->get('orderBy')) {
            $params['orderBy'] = $request->get('orderBy');
        } else if ($request->get('sortBy')) {
            $params['orderBy'] = $request->get('sortBy');
        }

        if ($request->get('order')) {
            $params['order'] = $request->get('order');
        } else if ($request->get('asc')) {
            $params['order'] = $request->get('asc') === 'true' ? 'asc' : 'desc';
        }

        if ($request->get('q')) {
            $params['q'] = trim($request->get('q'));
        }
        if ($request->get('textFilter')) {
            $params['textFilter'] = $request->get('textFilter');
        }
        if ($request->get('primaryFilter')) {
            $params['primaryFilter'] = $request->get('primaryFilter');
        }
        if ($request->get('boolFilterList')) {
            $params['boolFilterList'] = $request->get('boolFilterList');
        }
        if ($request->get('filterList')) {
            $params['filterList'] = $request->get('filterList');
        }

        if ($request->get('select')) {
            $params['select'] = explode(',', $request->get('select'));
        }
    }
}
