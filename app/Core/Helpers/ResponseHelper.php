<?php

namespace App\Core\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseHelper
{
    public static function renderCustomSuccessResponse($params = []): array
    {
        if (!empty($params['data'])) {
            $result['data'] = $params['data'];
        }
        if (!empty($params['meta'])) {
            $result['meta'] = $params['meta'];
        }
        $result['meta']['code'] = 200;
        $result['message'] = !empty($params['message']) ? $params['message'] : 'success';

        return $result;
    }

    public static function renderCustomErrorResponse($params): JsonResponse
    {

        $result['data']['success'] = false;
        $result['data']['message'] = !empty($params['message']) ? $params['message'] : 'failed';

        if (!empty($params['meta'])) {
            $result['meta'] = $params['meta'];
        }
        $result['meta']['code'] = !empty($params['code']) ? $params['code'] : Response::HTTP_BAD_REQUEST;

        return response()->json($result, !empty($params['code']) ? $params['code'] : Response::HTTP_BAD_REQUEST);
    }

    public static function render500Response(\Exception $ex): JsonResponse
    {
        \Log::info($ex);

        $result['data']['success'] = false;
        $result['data']['message'] = 'We have some issues, I mean.. who doesn\'t ?, and we\'re working on them, please try later';

        if (!empty($params['meta'])) {
            $result['meta'] = $params['meta'];
        }
        $result['meta']['code'] = Response::HTTP_INTERNAL_SERVER_ERROR;

        return response()->json($result, !empty($params['code']) ? $params['code'] : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function render404Response(): JsonResponse
    {

        $result['data']['success'] = false;
        $result['data']['message'] = 'Resource not found';

        if (!empty($params['meta'])) {
            $result['meta'] = $params['meta'];
        }
        $result['meta']['code'] = Response::HTTP_NOT_FOUND;

        return response()->json($result, !empty($params['code']) ? $params['code'] : Response::HTTP_NOT_FOUND);
    }
}
