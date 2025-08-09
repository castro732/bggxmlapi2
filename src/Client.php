<?php

namespace Nataniel\BoardGameGeek;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
/**
 * Class Client
 * @package Nataniel\BoardGameGeek
 * https://boardgamegeek.com/wiki/page/BGG_XML_API2
 */
class Client
{
    const API_URL = 'https://www.boardgamegeek.com/xmlapi2';

    private string $userAgent = 'BGG XML API Client/1.0';
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getThing(int $id, bool $stats = false): ?Thing
    {
        if (empty($id)) {
            return null;
        }

        $xml = $this->request('thing', [
            'id' => $id,
            'stats' => $stats,
        ]);

        return Factory::fromXml($xml->item);
    }

    /**
     * @return Thing[]
     */
    public function getThings(array $ids, bool $stats = false): array
    {
        if (empty($ids)) {
            return [];
        }

        $xml = $this->request('thing', [
            'id' => join(',', $ids),
            'stats' => $stats,
        ]);

        $items = [];
        foreach ($xml as $item) {
            $items[] = Factory::fromXml($item);
        }

        return $items;
    }

    /**
     * https://boardgamegeek.com/wiki/page/BGG_XML_API2#toc11
     * TODO: Note that you should check the response status code... if it's 202 (vs. 200) then it indicates BGG has queued
     * your request and you need to keep retrying (hopefully w/some delay between tries) until the status is not 202.
     * @return Collection|Collection\Item[]
     */
    public function getCollection(array $params): Collection
    {
        $xml = $this->request('collection', $params);
        if ($xml->getName() != 'items') {
            throw new Exception($xml->error->message);
        }

        return new Collection($xml);
    }

    /**
     * @return HotItem[]
     */
    public function getHotItems(string $type = Type::BOARDGAME): array
    {
        $xml = $this->request('hot', [
            'type' => $type,
        ]);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new HotItem($item);
        }

        return $items;
    }

    public function getUser(string $name): ?User
    {
        try {

        $xml = $this->request('user', [
            'name' => $name,
        ]);

        return !empty($xml['id'])
            ? new User($xml)
            : null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return Search\Query|Search\Result[]
     */
    public function search(string $query, bool $exact = false, string $type = Type::BOARDGAME): Search\Query
    {
        $xml = $this->request('search', array_filter([
            'query' => $query,
            'type' => $type,
            'exact' => (int) $exact,
        ]));

        return new Search\Query($xml);
    }

    /**
     * @return Play[]
     */
    public function getPlays(array $params): array
    {
        $xml = $this->request('plays', $params);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new Play($item);
        }

        return $items;
    }

    protected function request(string $action, array $params = []): \SimpleXMLElement
    {
        $url = sprintf('%s/%s?%s', self::API_URL, $action, http_build_query(array_filter($params)));
        $this->logger->debug('BGG API request', ['url' => $url, 'action' => $action, 'params' => $params]);

        $startTime = microtime(true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $duration = microtime(true) - $startTime;

        $this->logger->debug('BGG API response', [
            'code' => $httpCode,
            'duration' => round($duration, 2),
            'action' => $action,
            'size' => strlen($response),
        ]);

        curl_close($ch);

        if ($response === false) {
            $this->logger->error('BGG API call failed', [
                'error' => $curlError,
                'url' => $url,
                'action' => $action,
            ]);
            throw new Exception('API call failed: ' . $curlError);
        }

        if ($httpCode > 399) {
            $this->logger->error('BGG API error response', [
                'code' => $httpCode,
                'url' => $url,
                'action' => $action,
                'response' => substr($response, 0, 1000), // Log first 1000 chars of response
            ]);
            throw new Exception('API call failed with HTTP code ' . $httpCode);
        }

        // Handle 202 status code (request being processed)
        if ($httpCode === 202) {
            $this->logger->info('BGG API request queued', [
                'action' => $action,
                'params' => $params,
            ]);
            // implement retry logic here?
        }

        $xml = simplexml_load_string($response);
        if (!$xml instanceof \SimpleXMLElement) {
            $this->logger->error('Failed to parse BGG API response as XML', [
                'url' => $url,
                'action' => $action,
                'response' => substr($response, 0, 1000),
            ]);
            throw new Exception('Failed to parse API response as XML');
        }

        return $xml;
    }
}
