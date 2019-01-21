<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class APIResponse
 * Helper class to wrap data around a json response
 * Roughly adheres to JSend specification (https://labs.omniti.com/labs/jsend)
 */
trait APIResponse
{
    protected $responseType = 'json';

    /**
     * This function is the root function for building a proper JSON/XML response.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     * @param int $statusCode
     *
     * @return Response|JsonResponse
     */
    public function respondWith(array $data = [], string $message = 'Message', string $status = 'success', int $statusCode = 200)
    {
        if($this->responseType == 'xml') {
            if(!empty($data['xml'])) {
                return response($data['xml'], $statusCode)
                    ->header('Content-Type', 'text/xml');
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                $xml .= '<Response>';
                $xml .= '<Message>' . $message . '</Message>';
                $xml .= '<Status>' . $status . '</Status>';

                if(!empty($data)) {
                    foreach($data as $key => $value) {
                        // @TODO Find a better way to support associative arrays and use PHPs XML class as well as sanitize
                        if(is_numeric($key)) {
                            $xml .='<Key>' . $key . '</Key>';
                            if(is_array($value)) {
                                $xml .= '<Value>Array</Value>';
                            } else {
                                $xml .= '<Value>' . $value . '</Value>';
                            }
                        } else {
                            $xml .= '<' . $key . '>';
                            if(is_array($value)) {
                                $xml .= 'Array';
                            } else {
                                $xml .= $value;
                            }
                            $xml .= '</' . $key . '>';
                        }
                    }
                }
                $xml .= '</Response>';

                return response($xml, $statusCode)
                    ->header('Content-Type', 'text/xml');
            }
        } else {
            return response()->json([
                'message' => $message,
                'status' => $status,
                'data' => $data
            ], $statusCode);
        }
    }

    public function respondWithNothing(int $statusCode = 200)
    {
        if($this->responseType == 'xml') {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            return response($xml, $statusCode)
                ->header('Content-Type', 'text/xml');
        } else {
            return response()->json([], $statusCode);
        }
    }

    /**
     * 200 - OK
     * General status code. Most common code used to indicate success.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithOK(array $data = [], string $message = 'OK', string $status = 'success')
    {
        return $this->respondWith($data, $message, $status, 200);
    }

    /**
     * 201 - CREATED
     * Successful creation occurred (via either POST or PUT). Set the Location header to contain a link to the newly-created resource (on POST). Response body content may or may not be present.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithCreated(array $data = [], string $message = 'Created', string $status = 'success')
    {
        return $this->respondWith($data, $message, $status, 201);
    }

    /**
     * 204 - NO CONTENT
     * Indicates success but nothing is in the response body, often used for DELETE and PUT operations.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithNoContent(array $data = [], string $message = 'No Content', string $status = 'success')
    {
        return $this->respondWith($data, $message, $status, 204);
    }

    /**
     * 301 - MOVED PERMANENTLY
     * Indicates the requested resource is now permanently available at a different URI address.
     * If request method was something other than GET or HEAD the redirect MUST NOT happen automatically
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithMovedPermanently(array $data = [], string $message = 'Moved Permanently', string $status = 'success')
    {
        return $this->respondWith($data, $message, $status, 301);
    }

    /**
     * 302 - MOVED TEMPORARILY
     * Indicates the requested resource is temporarily available at a different URI address.
     * If request method was something other than GET or HEAD the redirect MUST NOT happen automatically
     *
     * Sometimes, incorrectly, used as a 303 request. 303-307 codes allow more advanced clients to dictate explicit responses.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithMovedTemporarily(array $data = [], string $message = 'Moved Temporarily', string $status = 'success')
    {
        return $this->respondWith($data, $message, $status, 302);
    }

    /**
     * 303 - SEE OTHER
     * Indicates a redirection based on the outcome of the previous request. This is not exactly the same as a 302, nor should it replace a 301 or 302 error.
     * It is used, for example, when the result of a POSTed URI results in a redirection to another GETable resource. (e.g. on creation, redirect to view page)
     * The implemenetation here should be used with some sort of HAL or HATEOS type _links array to specify the new target
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithSeeOther(array $data = [], string $message = 'Redirection', string $status = 'success')
    {
        return $this->respondWith($data, $message, $status, 303);
    }

    /**
     * 400 - BAD REQUEST
     * General error when fulfilling the request would cause an invalid state. Domain validation errors, missing payload, etc. are some examples.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithBadRequest(array $data = [], string $message = 'Bad Request', string $status = 'fail')
    {
        return $this->respondWith($data, $message, $status, 400);
    }

    /**
     * 401 - UNAUTHORIZED
     * Error code response for missing or invalid authentication token.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithUnauthorized(array $data = [], string $message = 'Unauthorized', string $status = 'fail')
    {
        return $this->respondWith($data, $message, $status, 401);
    }

    /**
     * 403 - FORBIDDEN
     * Error code for user not authorized to perform the operation or the resource is unavailable for some reason (e.g. time constraints, etc.).
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithForbidden(array $data = [], string $message = 'Forbidden', string $status = 'fail')
    {
        return $this->respondWith($data, $message, $status, 403);
    }

    /**
     * 404 - NOT FOUND
     * Used when the requested resource is not found, whether it doesn't exist or if there was a 401 or 403 that, for security reasons, the service wants to mask.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithNotFound(array $data = [], string $message = 'Not Found', string $status = 'fail')
    {
        return $this->respondWith($data, $message, $status, 404);
    }

    /**
     * 405 - METHOD NOT ALLOWED
     * Used to indicate that the requested URL exists, but the requested HTTP method is not applicable. For example, POST /users/12345 where the API doesn't support creation of resources this way (with a provided ID).
     * The Allow HTTP header must be set when returning a 405 to indicate the HTTP methods that are supported. In the previous case, the header would look like "Allow: GET, PUT, DELETE"
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithMethodNotAllowed(array $data = [], string $message = 'Method Not Allowed', string $status = 'fail')
    {
        return $this->respondWith($data, $message, $status, 405);
    }

    /**
     * 409 - CONFLICT
     * Whenever a resource conflict would be caused by fulfilling the request. Duplicate entries, such as trying to create two customers with the same information, and deleting root objects when cascade-delete
     * is not supported are a couple of examples.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithConflict(array $data = [], string $message = 'Conflict', string $status = 'fail')
    {
        return $this->respondWith($data, $message, $status, 409);
    }

    /**
     * 500 - INTERNAL SERVER ERROR
     * The server encountered an unexpected condition which prevented it from fulfilling the request. Should ONLY be used when unhandled exception occurs.
     *
     * @param array $data
     * @param string $message
     * @param string $status
     *
     * @return Response|JsonResponse
     */
    public function respondWithInternalServerError(array $data = [], string $message = 'Internal Server Error', string $status = 'error')
    {
        return $this->respondWith($data,  $message, $status, 500);
    }
}
