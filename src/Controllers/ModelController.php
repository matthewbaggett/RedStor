<?php

namespace RedStor\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedStor\Controllers\Traits;
use Slim\Http\Request;
use Slim\Http\Response;

class ModelController extends GatewayController
{
    use Traits\RedisClientTrait;

    /**
     * @route PUT v1/model/{modelName}
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function create(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $modelName = $request->getParsedBody()['name'];
        $result = $this->redStorClient->modelCreate($modelName);
        if(!(is_array($result) && $result[0] == 'OK')){
            return $this->jsonResponse([
                "Status" => "Fail",
                "Reason" => "Could not create model \"{$modelName}\".",
            ], $request, $response);
        }

        foreach($request->getParsedBody()['columns'] as $column){
            $columnAddResult = $this->redStorClient->modelAddColumn($modelName, $column['name'], $column['type']['name']);
            if(!(is_array($columnAddResult) && $columnAddResult[0] == 'OK')){
                return $this->jsonResponse([
                    "Status" => "Fail",
                    "Reason" => "Could not create column \"{$column['name']}\".",
                ], $request, $response);
            }
        }

        return $this->jsonResponse([
            "Status" => "Okay",
            "Model" => $this->redStorClient->rsDescribeModel($modelName),
        ], $request, $response);
    }
}
