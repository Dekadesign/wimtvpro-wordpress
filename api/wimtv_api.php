<?php

/**
 * Written by walter at 30/10/13
 */
include_once("api.php");

use Api\Api;
use \Httpful\Mime;
use \Httpful\Request;

function initApi($host, $username, $password) {
    Api::initApiAccessor($host, $username, $password);
}

function getApi() {
    return Api::getApiAccessor();
}

//GENERAL API
function apiCreateUrl($name) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('liveStream/uri?name=' . $name);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiEmbeddedLive($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('liveStream/' . $apiAccessor->username . '/' . $apiAccessor->username . '/hosts/' . $hostId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, Mime::JSON);
}

function apiGetVideoCategories() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videoCategories');
    return $apiAccessor->execute($request);
}

function apiGetUUID() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uuid');
    return $apiAccessor->execute($request);
}

function apiDownload($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->downloadRequest('videos/' . $hostId . '/download');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "");
}

function apiUpload($parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos?uploadIdentifier=' . $parameters['uploadIdentifier']);
    $request->body($parameters);
    $request->sends(Mime::UPLOAD);
    $request->attach(array('file' => $parameters['file']));
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetUploadProgress($contentIdentifier) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uploadProgress/' . $contentIdentifier);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

//PROFILE
function apiGetProfile() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('profile');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiEditProfile($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('profile');
    $request->sends(Mime::JSON);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiRegistration($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('register');
    $request->sendsJson();
    $request->body(json_encode($params));
    return $apiAccessor->execute($request);
}

function apiCheckPayment($cookie) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/payment/check');
    $request->setCookieFile($cookie);
    return $apiAccessor->execute($request);
}

//PACKET
function apiUpgradePacket($redirect_url, $cookie, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('userpacket/payment/pay?externalRedirect=true&success=' . $redirect_url);
    $request = $apiAccessor->authenticate($request);
    $request->setCookieJar($cookie);
    $request->sends(Mime::JSON);
    $request->body($params);
    $request->addHeader('Content-Lenght', strlen($params));
    return $apiAccessor->execute($request);
}

function apiGetPacket() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/' . $apiAccessor->username);
    return $apiAccessor->execute($request, Mime::JSON);
}

function apiCommercialPacket() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('commercialpacket');
    return $apiAccessor->execute($request, Mime::JSON);
}

//VIDEOS
function apiGetShowtimes() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username . '/showtime?details=true');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiPublishOnShowtime($id, $parameters) {
//    var_dump($id, $parameters);exit;
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiPublishAcquiredOnShowtime($id, $acquiredId, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/acquired/' . $acquiredId . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetThumbsVideo($contentId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $contentId . '/thumbnail');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml');
}

function apiGetDetailsVideo($contentId, &$error_response=null) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $contentId . '?details=true');
    $request = $apiAccessor->authenticate($request);
    $response = $apiAccessor->execute($request, "application/json");
    if ($response=="" && isset($error_response))
    {
        $error_response = $apiAccessor->execute($request);
    }
    return $response;
//    return $apiAccessor->execute($request);
}

function apiGetDetailsShowtime($id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username . '/showtime/' . $id . '/details');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiGetPlayerShowtime($id, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $id . "/embeddedPlayers?" . $parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetVideos($details = true) {
    if ($details) {
        $details = 'true';
    } else {
        $details = 'false';
    }
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos?details=' . $details);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiDeleteVideo($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $hostId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteFromShowtime($id, $stid) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $id . '/showtime/' . $stid);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

//LIVE
function apiChangePassword($password) {
    $apiAccessor = getApi();
    $request = $apiAccessor->putRequest("users/" . $apiAccessor->username . "/updateLivePwd");
    $params = array('liveStreamPwd' => $password);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetLiveEvents($timezone, $activeOnly) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '?timezone=' . $timezone;
    if ($activeOnly) {
        $url .= '&active=true';
    }
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiGetLive($host_id, $timezone = "") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiGetLiveIframe($host_id, $timezone = "") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id . '/embed';
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml');
}

function apiAddLive($parameters, $timezone = null) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl;
//    var_dump($parameters);
//PRINT "<HR>";
//var_dump($url);
//var_dump($timezone);
//exit;
    if ($timezone)
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->postRequest($url);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', false);
}

function apiModifyLive($host_id, $parameters, $timezone = null) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if ($timezone)
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->postRequest($url);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', false);
}

function apiDeleteLive($host_id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest($apiAccessor->liveHostsUrl . '/' . $host_id);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

//PROGRAMMING
function apiProgrammingPool() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programmingPool');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetCurrentProgrammings($qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('currentProgramming?' . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiPostProgrammings($qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest("programmings");
    $request = $apiAccessor->authenticate($request);
    $request->body($qs);
    return $apiAccessor->execute($request);
}

function apiGetProgrammings() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programmings');
    $request = $apiAccessor->authenticate($request);
    $request->sendsAndExpects(Mime::JSON);
    $request->addOnCurlOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    return $apiAccessor->execute($request);
}

function apiRemoveItemProgramming($progId, $qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId . "?" . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteProgramming($progId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDetailsProgramming($programming_id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programming/' . $programming_id);
    $request = $apiAccessor->authenticate($request);
    $request->sendsAndExpects(Mime::JSON);
    $request->addOnCurlOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    return $apiAccessor->execute($request, 'application/json');
}

function apiAddItem($progId, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('programming/' . $progId . '/items');
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetCalendar($progId, $qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest("programming/" . $progId . "/calendar?" . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteItems($progId, $itemId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId . "/item/" . $itemId);
    $request = $apiAccessor->authenticate($request);

    return $apiAccessor->execute($request);
}

function apiUpdateItems($progId, $itemId, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest("programming/" . $progId . "/item/" . $itemId);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiMimicItem($progId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest("programming/" . $progId . "/mimic");
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));
?>