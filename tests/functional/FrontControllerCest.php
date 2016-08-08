<?php

class FrontControllerCest
{
    private $_sha;

    public function _before(FunctionalTester $I)
    {
        $appKey = env('EMBER_APP');

        // the "app:current" version of an ember application
        $html = '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head><title>Test App</title></head>';
        $html .= '<body><h1>Welcome to Ember</h1></body>';
        $html .= '</html>';
        $I->haveInRedis('string', "$appKey:current", $html);

        // the "app:<sha>" version of an ember application
        // $shaHtml = '<!DOCTYPE html>';
        // $shaHtml .= '<html>';
        // $shaHtml .= '<head><title>Test App</title></head>';
        // $shaHtml .= '<body><h1>Welcome to Ember - Updated Code</h1></body>';
        // $shaHtml .= '</html>';
        //
        // $sha = 'abc1234';
        // $this->_sha = $sha;
        // $I->haveInRedis('string', "$appKey:$sha", $shaHtml);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function fetchCurrent(FunctionalTester $I)
    {
        $I->wantTo('Fetch the latest version of the application');
        // $I->amOnPage('/');
        // $I->seeResponseCodeIs(200);
        // $I->seeInTitle('Test App');
        // $I->seeInField('h1', 'Welcome to Ember');
    }

    // public function fetchSha(FunctionalTester $I)
    // {
    //     $I->wantTo('Fetch a known SHA version of the app');
    // }
    //
    // public function fetchUnknownSha(FunctionalTester $I)
    // {
    //     $I->wantTo('Verify an unknown SHA throws an exception');
    // }
    //
    // public function fetchWithQueryString()
    // {
    //     $I->wantTo('Fetch the latest version and the URL has a query string');
    // }
    //
    // public function fetchNonIndex()
    // {
    //     $I->wantTo('Fetch the latest version that isn\'t the root');
    // }
}
