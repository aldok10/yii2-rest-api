<?php

namespace app\modules\v1\controllers;

use app\core\exceptions\InvalidArgumentException;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\base\Model;
use yii\filters\Cors;
use yiier\helpers\SearchModel;
use yiier\helpers\Setup;

class ActiveController extends \yii\rest\ActiveController
{
    protected const MAX_PAGE_SIZE = 100;
    protected const DEFAULT_PAGE_SIZE = 20;

    /**
     * Not involved in verification actions
     * @var array
     */
    public $noAuthActions = [];

    // serialized output
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Cross-region requests must be deleted first authenticator
        $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ]
        ];
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => array_merge($this->noAuthActions, ['options']),
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $modelClass = $this->modelClass;

        $searchModel = new SearchModel(
            [
                'defaultOrder' => ['id' => SORT_DESC],
                'model' => $modelClass,
                'scenario' => 'default',
                'pageSize' => $this->getPageSize()
            ]
        );

        return $searchModel->search(['SearchModel' => request()->getQueryParams()]);
    }

    /**
     * @return int
     */
    protected function getPageSize()
    {
        if ($pageSize = (int)request('pageSize')) {
            if ($pageSize < self::MAX_PAGE_SIZE) {
                return $pageSize;
            }
            return self::MAX_PAGE_SIZE;
        }
        return self::DEFAULT_PAGE_SIZE;
    }


    /**
     * @param Model|string $model
     * @param array $params
     * @return Model
     * @throws InvalidArgumentException
     */
    public function validate(Model|string $model, array $params = null, int $code = 400): Model
    {
        if (is_null($params)) {
            $req = request();
            $params = $req?->getIsGet() ? $req?->getQueryParams() : $req?->getBodyParams();
        }

        if (is_string($model) && is_subclass_of($model, Model::class)) {
            $model = new $model();
        }

        $model->load($params, '');
        if (!$model->validate()) {
            throw new InvalidArgumentException(Setup::errorMessage($model->firstErrors), $code);
        }
        return $model;
    }
}
