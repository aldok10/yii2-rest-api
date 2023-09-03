<?php

namespace app\tests\api;

// @codingStandardsIgnoreLine
use ApiTester;
use Codeception\Util\HttpCode;

class TopTransactionsPerUserCest
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
    public function topUserViaAPIFailInvalidToken(ApiTester $I)
    {
        $token = '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/top-transactions-per-user');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => HttpCode::UNAUTHORIZED]);
    }


    /**
     * @param ApiTester $I
     * @param TransferCest $transferCest
     * @param BalanceReadCest $balanceReadCest
     * @param BalanceTopUpCest $balanceTopUpCest
     * @param CreateUserCest $createUserCest
     */
    public function topUsersViaAPISuccess(
        ApiTester $I,
        TransferCest $transferCest,
        BalanceReadCest $balanceReadCest,
        BalanceTopUpCest $balanceTopUpCest,
        CreateUserCest $createUserCest,
    ) {
        $response = $transferCest->transferViaAPISuccess($I, $balanceReadCest, $balanceTopUpCest, $createUserCest);
        $token = $response['token'] ?? '-- Invalid Token --';

        $I->haveHttpHeader('content-type', 'application/json');
        $I->haveHttpHeader('Authorization', "Bearer {$token}");
        $I->sendPost('/user/top-transactions-per-user');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesXpath('//data/username');
        $I->seeResponseJsonMatchesXpath('//data/amount');

        return ['token' => $token];
    }
}
