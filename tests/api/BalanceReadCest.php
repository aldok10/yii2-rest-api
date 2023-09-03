<?php

namespace app\tests\api;

// @codingStandardsIgnoreLine
use ApiTester;
use Codeception\Util\HttpCode;
use yii\helpers\Json;

class BalanceReadCest
{
    /**
     * @codingStandardsIgnoreStart
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        // @codingStandardsIgnoreEnd
    }

    /**
     * @param ApiTester $I
     */
    public function balanceReadViaAPIFailInvalidToken(ApiTester $I)
    {
        $token = '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendGet('/user/balance-read');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => HttpCode::UNAUTHORIZED]);
    }


    /**
     * @param ApiTester $I
     * @param CreateUserCest $createUserCest
     */
    public function balanceReadViaAPISuccess(ApiTester $I, CreateUserCest $createUserCest)
    {
        $response = $createUserCest->createUserViaAPISuccess($I);
        $token = $response['data']['token'] ?? '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendGet('/user/balance-read');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => 0]);
        $I->seeResponseJsonMatchesXpath('//data/amount');

        return  [
            'token' => $token,
            'balanceRead' => Json::decode(
                json: $I->grabResponse(),
                asArray: true
            )
        ];
    }
}
