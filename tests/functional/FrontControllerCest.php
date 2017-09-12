<?php

use Lang;

class FrontControllerCest
{
    private $_appKey;
    private $_currentSha;
    private $_updatedSha;

    public function _before(FunctionalTester $I)
    {
        $this->_appKey = env('EMBER_APP');

        // the "app:current" version of an ember application
        $html = '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head><title>Test App</title></head>';
        $html .= '<body><h1>Welcome to Ember</h1></body>';
        $html .= '</html>';

        $this->_currentSha = 'abcdefg';
        $I->haveInRedis('string', "$this->_appKey:current", $this->_currentSha);
        $I->haveInRedis('string', "$this->_appKey:$this->_currentSha", $html);

        // the "app:<sha>" version of an ember application
        $shaHtml = '<!DOCTYPE html>';
        $shaHtml .= '<html>';
        $shaHtml .= '<head><title>Test App</title></head>';
        $shaHtml .= '<body><h1>Welcome to Ember - Updated Code</h1></body>';
        $shaHtml .= '</html>';

        $this->_updatedSha = 'xyz0000';
        $I->haveInRedis('string', "$this->_appKey:$this->_updatedSha", $shaHtml);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function fetchCurrent(FunctionalTester $I)
    {
        $I->wantTo('Fetch the latest version of the application');
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->seeInTitle('Test App');
        $I->seeElement('h1');
        $h1 = $I->grabTextFrom('h1');
        $I->assertEquals($h1, 'Welcome to Ember');
    }

    public function fetchSha(FunctionalTester $I)
    {
        $I->wantTo('Fetch a known SHA version of the app');
        $I->amOnPage("/?key=$this->_appKey:$this->_updatedSha");
        $I->seeResponseCodeIs(200);
        $I->seeInTitle('Test App');
        $I->seeElement('h1');
        $h1 = $I->grabTextFrom('h1');
        $I->assertEquals($h1, 'Welcome to Ember - Updated Code');
    }

    public function fetchUnknownSha(FunctionalTester $I)
    {
        $I->wantTo('Verify an unknown SHA throws an exception');
        $I->amOnPage("/?key=$this->_appKey:00000000");
        $I->seeResponseCodeIs(200);
        $I->see('We are experiencing some technical difficulties');
    }

    public function fetchWithQueryString(FunctionalTester $I)
    {
        $I->wantTo('Fetch the latest version and the URL has a query string');
        $I->amOnPage('/?some-random-compliment=you-look-nice-today');
        $I->seeResponseCodeIs(200);
        $I->seeInTitle('Test App');
        $I->seeElement('h1');
        $h1 = $I->grabTextFrom('h1');
        $I->assertEquals($h1, 'Welcome to Ember');
    }

    public function fetchNonIndex(FunctionalTester $I)
    {
        $I->wantTo('Fetch the latest version that isn\'t the root');
        $I->amOnPage('/another/url');
        $I->seeResponseCodeIs(200);
        $I->seeInTitle('Test App');
        $I->seeElement('h1');
        $h1 = $I->grabTextFrom('h1');
        $I->assertEquals($h1, 'Welcome to Ember');
    }

	public function fetchUrlEncodedString(FunctionalTester $I)
    {
        $I->wantTo('Fetch the latest version that isn\'t the root');
        $I->amOnPage('/another/url/with%2Bencoded%40strings.net');
        $I->seeResponseCodeIs(200);
        $I->seeInTitle('Test App');
        $I->seeElement('h1');
        $h1 = $I->grabTextFrom('h1');
        $I->assertEquals($h1, 'Welcome to Ember');
    }
}
