<?php

namespace app\tests\api;

use ApiTester;
use Codeception\Example;
use Codeception\Util\HttpCode;
use yii\helpers\Json;

class CreateUserCest
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
    public function makeValidateBadRequestItems()
    {
        return [
            [
                'data' => [
                    'username' => 'demo-sdsdkj',
                    'password' => 'pass123',
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Username can only be numbers and letters.',
            ],
            [
                'data' => [
                    'username' => 'demo-sdsdkj',
                    'password' => 'pass1',
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Username can only be numbers and letters.',
            ],
            [
                'data' => [
                    'username' => 'demo-sdsdkj',
                    'password' => 'pass1',
                ],
                'code' => HttpCode::BAD_REQUEST,
                'message' => 'Username can only be numbers and letters.',
            ],
        ];
    }

    /**
     * @dataProvider makeValidateBadRequestItems
     * @param ApiTester $I
     * @param Example $example
     */
    public function createUserViaAPIFail(ApiTester $I, Example $example)
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/create', $example['data']);
        $I->seeResponseCodeIs($example['code']);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => $example['code']]);
        $I->seeResponseContainsJson(['message' => $example['message']]);
    }

    /**
     * @return array
     */
    public function makeValidateUsernameAlreadyExistItems()
    {
        return [
            [
                'data' => [
                    'username' => 'demo',
                    'password' => 'pass123',
                ],
                'code' => [
                    'success' => HttpCode::CREATED,
                    'fail' => HttpCode::CONFLICT,
                ],
                'message' => [
                    'success' => 'Success Message',
                    'fail' => 'Username already exists',
                ],
            ],
        ];
    }

    /**
     * @dataProvider makeValidateUsernameAlreadyExistItems
     * @param ApiTester $I
     * @param Example $example
     */
    public function createUserViaAPIFailUsernameAlreadyExists(ApiTester $I, Example $example)
    {
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/create', $example['data']);
        $I->seeResponseCodeIs($example['code']['success']); // 201 Created
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => 0]);
        $I->seeResponseContainsJson(['message' => $example['message']['success']]);

        // Re-submit with same username password
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/create', $example['data']);
        $I->seeResponseCodeIs($example['code']['fail']); // 409 Conflict
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => $example['code']['fail']]);
        $I->seeResponseContainsJson(['message' => $example['message']['fail']]);
    }

    /**
     * @param ApiTester $I
     * @param array $userAccount
     */
    public function createUserViaAPISuccess(ApiTester $I, array $userAccount = null)
    {
        if ($userAccount === null) {
            $userAccount = ['username' => 'demo', 'password' => 'pass123'];
        }

        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendPOST('/user/create', $userAccount);
        $I->seeResponseCodeIs(HttpCode::CREATED); // 201
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['code' => 0]);

        // contains user data
        $I->seeResponseJsonMatchesXpath('//data/user/id');
        $I->seeResponseJsonMatchesXpath('//data/user/username');
        $I->seeResponseJsonMatchesXpath('//data/user/status');
        $I->seeResponseJsonMatchesXpath('//data/user/created_at');
        $I->seeResponseJsonMatchesXpath('//data/user/updated_at');

        // contains balance data
        $I->seeResponseJsonMatchesXpath('//data/balance/id');
        $I->seeResponseJsonMatchesXpath('//data/balance/user_id');
        $I->seeResponseJsonMatchesXpath('//data/balance/amount');
        $I->seeResponseJsonMatchesXpath('//data/balance/created_at');
        $I->seeResponseJsonMatchesXpath('//data/balance/updated_at');

        // contains token data
        $I->seeResponseJsonMatchesXpath('//data/token');

        return Json::decode(
            json: $I->grabResponse(),
            asArray: true,
        );
    }
}
