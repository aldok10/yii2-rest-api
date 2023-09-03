<?php

namespace app\tests\api;

// @codingStandardsIgnoreLine
use ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;
use yii\helpers\Json;

class BalanceTopupCest
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
     * @return array
     */
    public function makeValidateFailItems()
    {
        return [
            [
                'data' => ['amount' => null],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount cannot be blank.',
            ],
            [
                'data' => ['amount' => -15000],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be no less than 0.01.',
            ],
            [
                'data' => ['amount' => 0],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be no less than 0.01.',
            ],
            [
                'data' => ['amount' => 'test'],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be a number.',
            ],
            [
                'data' => ['amount' => 100000000],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be no greater than 10000000.',
            ],
        ];
    }

    /**
     * @dataProvider makeValidateFailItems
     * @param ApiTester $I
     * @param BalanceReadCest $balanceReadCest
     * @param CreateUserCest $createUserCest
     * @param Example $example
     */
    public function balanceTopupViaAPIFailValidate(
        ApiTester $I,
        BalanceReadCest $balanceReadCest,
        CreateUserCest $createUserCest,
        Example $example,
    ) {
        $response = $balanceReadCest->balanceReadViaAPISuccess($I, $createUserCest);
        $token = $response['token'] ?? '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/balance-topup', $example['data']);
        $I->seeResponseCodeIs($example['code']); // 400
        $I->seeResponseContainsJson(['code' => $example['code']]);
        $I->seeResponseContainsJson(['message' => $example['message']]);
    }

    /**
     * @param ApiTester $I
     */
    public function balanceTopupViaAPIFailInvalidToken(ApiTester $I)
    {
        $token = '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/balance-topup');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => HttpCode::UNAUTHORIZED]);
    }


    /**
     * @param ApiTester $I
     * @param BalanceReadCest $balanceReadCest
     * @param CreateUserCest $createUserCest
     */
    public function balanceTopupViaAPISuccess(
        ApiTester $I,
        BalanceReadCest $balanceReadCest,
        CreateUserCest $createUserCest,
    ) {
        $response = $balanceReadCest->balanceReadViaAPISuccess($I, $createUserCest);
        $token = $response['token'] ?? '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/balance-topup', [
            'amount' => 998877,
        ]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT); // 204

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendGet('/user/balance-read');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data/amount');
        $I->seeResponseContainsJson(['code' => 0]);
        $I->seeResponseContainsJson(['data' => ['amount' => "998877.00"]]);

        return  [
            'token' => $token,
            'balanceRead' => Json::decode(
                json: $I->grabResponse(),
                asArray: true
            )
        ];
    }
}
