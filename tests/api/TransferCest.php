<?php

namespace app\tests\api;

// @codingStandardsIgnoreLine
use ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;

class TransferCest
{
    public const ACCOUNT_FOR_TRANSFER_BALANCE = 'account1';

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
                'data' => [
                    'amount' => null,
                    'to_username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount cannot be blank.',
            ],
            [
                'data' => [
                    'amount' => 1000,
                    'to_username' => null,
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'To Username cannot be blank.',
            ],
            [
                'data' => [
                    'amount' => 1000,
                    'to_username' => 'invalidusername',
                ],
                'code' => HttpCode::NOT_FOUND,
                'message' => 'Destination user not found',
            ],
            [
                'data' => [
                    'amount' => -15000,
                    'to_username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be no less than 0.01.',
            ],
            [
                'data' => [
                    'amount' => 0,
                    'to_username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be no less than 0.01.',
            ],
            [
                'data' => [
                    'amount' => 'test',
                    'to_username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be a number.',
            ],
            [
                'data' => [
                    'amount' => 100000000,
                    'to_username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Amount must be no greater than 10000000.',
            ],
        ];
    }

    /**
     * @dataProvider makeValidateFailItems
     * @param ApiTester $I
     * @param BalanceReadCest $balanceReadCest
     * @param BalanceTopUpCest $balanceTopUpCest
     * @param CreateUserCest $createUserCest
     * @param Example $example
     */
    public function transferViaAPIFailValidate(
        ApiTester $I,
        BalanceReadCest $balanceReadCest,
        BalanceTopUpCest $balanceTopUpCest,
        CreateUserCest $createUserCest,
        Example $example,
    ) {
        // Create user for transfer balance to other account
        $createUserCest->createUserViaAPISuccess($I, [
            'username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
            'password' => 'pass123'
        ]);

        // Topup via api
        $response = $balanceTopUpCest->balanceTopupViaAPISuccess($I, $balanceReadCest, $createUserCest);
        $token = $response['token'] ?? '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/transfer', $example['data']);
        $I->seeResponseCodeIs($example['code']); // 400
        $I->seeResponseContainsJson(['code' => $example['code']]);
        $I->seeResponseContainsJson(['message' => $example['message']]);
    }

    /**
     * @param ApiTester $I
     */
    public function transferViaAPIFailInvalidToken(ApiTester $I)
    {
        $token = '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/transfer');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => HttpCode::UNAUTHORIZED]);
    }


    /**
     * @param ApiTester $I
     * @param BalanceReadCest $balanceReadCest
     * @param BalanceTopUpCest $balanceTopUpCest
     * @param CreateUserCest $createUserCest
     */
    public function transferViaAPISuccess(
        ApiTester $I,
        BalanceReadCest $balanceReadCest,
        BalanceTopUpCest $balanceTopUpCest,
        CreateUserCest $createUserCest,
    ) {
        // Create user for transfer balance to other account
        $createUserCest->createUserViaAPISuccess($I, [
            'username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
            'password' => 'pass123'
        ]);

        // Topup via api
        $response = $balanceTopUpCest->balanceTopupViaAPISuccess($I, $balanceReadCest, $createUserCest);
        $token = $response['token'] ?? '-- Invalid Token --';

        $amount = floatval($response['balanceRead']['data']['amount']);

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendGet('/user/balance-read');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'amount' => number_format($amount, 2, '.', ''),
            ],
        ]);

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/transfer', [
            'amount' => 1000,
            'to_username' => self::ACCOUNT_FOR_TRANSFER_BALANCE,
        ]);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT); // 204

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendGet('/user/balance-read');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'data' => [
                'amount' => number_format($amount - 1000, 2, '.', ''),
            ],
        ]);

        return  ['token' => $token];
    }
}
