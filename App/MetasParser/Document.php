<?php

namespace App\MetasParser;

class Document
{
	/**
	 * @var \DOMDocument
	 */
	private $DOM;

	/**
	 * @var array
	 */
	private $metas = [];

	/**
	 * @var string
	 */
	private $title = "";

	/**
	 * @var string
	 */
	private $description = "";

	/**
	 * @var array
	 */
	private $keywords = [];

	/**
	 * @var string
	 */
	private $generator;

	/**
	 * @var array
	 */
	private $ogFields = [
		[
			'field' => 'og:title'
		],
		[
			'field' => 'og:site_name'
		],
		[
			'field' => 'og:video'
		],
		[
			'field' => 'og:description'
		],
		[
			'field' => 'og:locale'
		],
		[
			'field' => 'og:locale:alternate'
		],
		[
			'field' => 'og:image'
		],
		[
			'field' => 'og:url'
		],
		[
			'field' => 'og:type'
		],
		[
			'field' => 'article:published_time'
		],
		[
			'field' => 'article:modified_time'
		],
		[
			'field' => 'article:expiration_time'
		],
		[
			'field' => 'article:author'
		],
		[
			'field' => 'article:section'
		],
		[
			'field' => 'article:tag'
		]
	];

	/**
	 * @var array
	 */
	private $parsedOgMetas = [];

	public function __construct(\DOMDocument $DOM)
	{
		$this->DOM = $DOM;
	}

	private function parseMetas()
	{
		$metas = $this->metas;
		if ($this->DOM->getElementsByTagName('meta')->length > 0) {
			foreach ($this->DOM->getElementsByTagName('meta') as $meta) {
				array_push($metas, [
					'name' => $meta->getAttribute('name'),
					'content' => $meta->getAttribute('content'),
					'property' => $meta->getAttribute('property')
				]);
			}
		}

		return $metas;
	}

	private function parseTitle()
	{
		if ($this->DOM->getElementsByTagName('title')->length > 0) {
			return $this->DOM->getElementsByTagName('title')[0]->textContent;
		} else {
			return NULL;
		}
	}

	private function parseDescription()
	{
		return $this->filterMetas($this->metas, "description", "name");
	}

	private function parseGenerator()
	{
		return $this->filterMetas($this->metas, "generator", "name");
	}


	private function parseKeywords()
	{
		$keywords = $this->filterMetas($this->metas, "keywords", "name");
		if ($keywords !== NULL) {
			return array_map(function ($item) {
				if (substr($item, 0, 1) == " ") {
					return substr($item, 1);
				} else {
					return $item;
				}
			}, explode(',', $keywords));
		} else {
			return NULL;
		}
	}

	private function parseOgMetas()
	{
		$parsedOgMetas = [];
		foreach ($this->ogFields as $ogField) {
			$result = $this->filterMetas($this->metas, $ogField['field'], 'property');
			if ($result != NULL) {
				$parsedOgMetas[$ogField['field']] = $result;
			}
		}
		if (count($parsedOgMetas) == 0) {
			foreach ($this->ogFields as $ogField) {
				$result = $this->filterMetas($this->metas, $ogField['field'], 'name');
				if ($result != NULL) {
					$parsedOgMetas[$ogField['field']] = $result;
				}
			}
		}

		return $parsedOgMetas;
	}

	private function filterMetas($array, $needed, $field, $retrieve = "content", $getArray = false)
	{
		if (isset(array_values(array_filter($array, function ($val) use ($needed, $field) {
				return $val[$field] == $needed;
			}))[0])) {
			$result = array_values(array_filter($array, function ($val) use ($needed, $field) {
				return $val[$field] == $needed;
			}));
			if (count($result) > 1) {
				return array_map(function ($array) {
					return $array['content'];
				}, $result);
			} else {
				return array_values(array_filter($array, function ($val) use ($needed, $field) {
					return $val[$field] == $needed;
				}))[0][$retrieve];
			}
		} else {
			return NULL;
		}
	}

	public function parse()
	{
		$this->metas = $this->parseMetas();
		$this->title = $this->parseTitle();
		$this->generator = $this->parseGenerator();
		$this->description = $this->parseDescription();
		$this->keywords = $this->parseKeywords();
		$this->parsedOgMetas = $this->parseOgMetas();
	}

	public function toArray()
	{
		return [
			'title' => $this->title,
			'description' => $this->description,
			'generator' => $this->generator,
			'keywords' => $this->keywords,
			'og' => $this->parsedOgMetas
		];
	}

	public function getMetas()
	{
		return $this->metas;
	}
}