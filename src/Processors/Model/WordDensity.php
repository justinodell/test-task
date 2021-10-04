<?php

namespace App\Processors\Model;

use App\Utils\Utils;

class WordDensity implements \JsonSerializable
{
    /**
     * @var string|null
     */
    private ?string $url;

    /**
     * @var int|null
     */
    private ?int $limit;

    public function __construct(?string $url, ?int $limit = 100)
    {
        $this->url = $url;
        $this->limit = $limit;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return WordDensity
     */
    public function setUrl(?string $url): WordDensity
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     *
     * @return WordDensity
     */
    public function setLimit(?int $limit): WordDensity
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'limit' => $this->limit,
        ];
    }

    /**
     * @param string $json
     *
     * @return self
     */
    public static function jsonDeserialize(string $json): self
    {
        $data = array_replace([
            'url' => null,
            'limit' => null,
        ], Utils::jsonDecode($json, true));

        if (!isset($data['url'])) {
            throw new \LogicException('The message does not contain "url" but it is required.');
        }

        return new static($data['url'], $data['limit']);
    }
}
