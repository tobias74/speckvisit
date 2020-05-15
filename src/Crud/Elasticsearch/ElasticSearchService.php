<?php

namespace Speckvisit\Crud\Elasticsearch;

class ElasticSearchService
{
    protected $mapper;
    protected $config;

    public function __construct($config, $mapper)
    {
        $this->config = $config;
        $this->mapper = $mapper;
    }

    protected function getEntityWords($words, $offset)
    {
        $commandWords = array_slice($words, $offset);
        $entityWords = array();
        $lastWord = '';
        foreach ($commandWords as $word) {
            if (('And' === $word) || ('Or' === $word)) {
                $entityWords[] = $lastWord;
                $entityWords[] = $word;
                $lastWord = '';
            } else {
                $lastWord .= $word;
            }
        }
        $entityWords[] = $lastWord;

        return $entityWords;
    }

    protected function makeSpecification($entityWords, $arguments)
    {
        if ((false !== array_search('And', $entityWords)) && (false !== array_search('Or', $entityWords))) {
            throw new \ErrorException('Error: Combined Or and And? '.print_r($entityWords, true));
        } elseif (false !== array_search('And', $entityWords)) {
            $operation = 'And';
        } elseif (false !== array_search('Or', $entityWords)) {
            $operation = 'Or';
        } else {
            // this is ok, it's just one word
        }

        $combinedWords = array_merge(array_diff($entityWords, ['And', 'Or']));

        $criteriaMaker = new \Speckvisit\Specification\CriteriaMaker();
        foreach ($combinedWords as $index => $entityWord) {
            if (!isset($criteria)) {
                $criteria = $criteriaMaker->equals(lcfirst($combinedWords[$index]), $arguments[$index]);
            } else {
                $command = 'logical'.$operation;
                $criteria = $criteria->$command($criteriaMaker->equals(lcfirst($combinedWords[$index]), $arguments[$index]));
            }
        }

        return $criteria;
    }

    public function __call($name, $arguments)
    {
        $words = $this->splitByCamelCase($name);

        if (('get' === $words[0]) && ('By' === $words[1])) {
            $entityWords = $this->getEntityWords($words, 2);
            $criteria = $this->makeSpecification($entityWords, $arguments);

            return $this->getBySpecification($criteria);
        } elseif (('get' === $words[0]) && ('One' === $words[1]) && ('By' === $words[2])) {
            $entityWords = $this->getEntityWords($words, 3);
            $criteria = $this->makeSpecification($entityWords, $arguments);

            return $this->getOneBySpecification($criteria);
        } else {
            throw new \ErrorException('Method not found '.$name);
        }
    }

    protected function splitByCamelCase($camelCaseString)
    {
        $re = '/(?<=[a-z]|[0-9])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);

        return $a;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getMapper()
    {
        return $this->mapper;
    }

    protected function getClient()
    {
        $hosts = [
      $this->getConfig()['elasticSearchHost'],
    ];

        $clientBuilder = \Elasticsearch\ClientBuilder::create();
        $clientBuilder->setHosts($hosts);
        $client = $clientBuilder->build();

        return $client;
    }

    public function createIndex()
    {
        $indexParams = $this->getMapper()->getCreateIndexCommand();
        $this->getClient()->indices()->create($indexParams);
    }

    public function createPercolatorIndex()
    {
        $indexParams = $this->getMapper()->getCreatePercolatorIndexCommand();
        $this->getClient()->indices()->create($indexParams);
    }

    public function getIndexName()
    {
        return $this->getMapper()->getIndexName();
    }

    protected function mapEntityToHash($station)
    {
        return $this->getMapper()->mapEntityToHash($station);
    }

    public function indexEntityWithoutRefresh($entity)
    {
        $params = array();
        $params['body'] = $this->mapEntityToHash($entity);
        $params['index'] = $this->getIndexName();
        $params['id'] = $entity->getId();
        //$params['client']['future'] = 'lazy';

        $returnValue = $this->getClient()->index($params);

        return $returnValue;
    }

    public function indexEntity($entity)
    {
        $this->indexEntityWithoutRefresh($entity);

        $this->getClient()->indices()->refresh(array(
      'index' => $this->getIndexName(),
    ));
    }

    public function indexQuery($criteria, $meta)
    {
        $body = array(
      'query' => $this->getFilter($criteria),
      'meta' => $meta,
    );

        $params = array();
        $params['body'] = $body;
        $params['index'] = $this->getIndexName().'_percolator';

        $returnValue = $this->getClient()->index($params);
    }

    public function percolateEntity($entity)
    {
        $hash = $this->mapEntityToHash($entity);
        $body = array(
      'query' => array(
        'percolate' => array(
            'field' => 'query',
            'document' => $hash,
        ),
      ),
    );

        $params = array();
        $params['index'] = $this->getIndexName().'_percolator';
        $params['body'] = $body;
        $responseArray = $this->getClient()->search($params);

        return array_map(function ($hit) {
            return $hit['_source']['meta'];
        }, $responseArray['hits']['hits']);
    }

    public function deleteEntity($entity)
    {
        $deleteParams = array();
        $deleteParams['index'] = $this->getIndexName();
        $deleteParams['id'] = $entity->getId();
        $deleteParams['refresh'] = true;
        $retDelete = $this->getClient()->delete($deleteParams);
    }

    public function getOneBySpecification($elasticSpec)
    {
        $entities = $this->getBySpecification($elasticSpec);
        if (count($entities) > 1) {
            throw \Exception('too many results');
        } elseif (0 === count($entities)) {
            throw new \Exception('no results');
        } else {
            return $entities[0];
        }
    }

    protected function getFilter($filterCriteria)
    {
        $criteriaVisitor = new ElasticsearchFilterCriteriaVisitor($this->getMapper());
        $filterCriteria->acceptVisitor($criteriaVisitor);
        $filter = $criteriaVisitor->getArrayForCriteria($filterCriteria);

        return $filter;
    }

    protected function getDeleteParams($elasticSpec)
    {
        $query = array(
      'query' => $this->getFilter($elasticSpec['criteria']),
    );

        $params = array();
        $params['index'] = $this->getIndexName();
        $params['body'] = $query;

        return $params;
    }

    protected function getSearchParams($elasticSpec)
    {
        $query = array(
          'query' => $this->getFilter($elasticSpec['criteria']),
        );

        if (isset($elasticSpec['offset'])) {
            $query['from'] = $elasticSpec['offset'];
        }

        if (isset($elasticSpec['limit'])) {
            $query['size'] = $elasticSpec['limit'];
        }

        if (isset($elasticSpec['sort'])) {
            $query['sort'] = $elasticSpec['sort'];
        }

        if (isset($elasticSpec['search_after'])) {
            $query['search_after'] = $elasticSpec['search_after'];
        }

        $params = array();
        $params['index'] = $this->getIndexName();
        $params['body'] = $query;

        return $params;
    }

    public function getBySpecification($elasticSpec)
    {
        $params = $this->getSearchParams($elasticSpec);
        try {
            $responseArray = $this->getClient()->search($params);
        } catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) {
            echo json_encode($params);
            die();
        }

        $finalResponse = array();

        foreach ($responseArray['hits']['hits'] as $index => $data) {
            $entity = $this->mapHashToEntity($data);
            $finalResponse[$index] = $entity;
        }

        return $finalResponse;
    }

    protected function mapHashToEntity($stationData)
    {
        return $this->getMapper()->mapHashToEntity($stationData);
    }

    public function deleteBySpecification($elasticSpec, $params = array())
    {
        $params = array_merge($this->getDeleteParams($elasticSpec), $params);
        $result = $this->getClient()->deleteByQuery($params);

        return $result;
    }

    public function aggregate($criteria, $aggregation)
    {
        $criteriaVisitor = new ElasticsearchFilterCriteriaVisitor($this->getMapper());
        $criteria->acceptVisitor($criteriaVisitor);
        $filter = $criteriaVisitor->getArrayForCriteria($criteria);

        $aggregationHash = [
      'field' => $this->getMapper()->getColumnForField($aggregation['field']),
    ];

        if (isset($aggregation['size'])) {
            $aggregationHash['size'] = $aggregation['size'];
        }

        $query = array();
        $query['aggs'] = [
      'myAggWrapper' => [
        'filter' => $filter,
        'aggs' => [
          'myAggName' => [
            $aggregation['type'] => $aggregationHash,
          ],
        ],
      ],
    ];
        $params = array();
        $params['index'] = $this->getIndexName();
        $params['body'] = $query;

        $responseArray = $this->getClient()->search($params);

        return $responseArray['aggregations']['myAggWrapper']['myAggName'];
    }

    public function aggregatePassThroughDirectly($criteria, $aggregation)
    {
        $criteriaVisitor = new ElasticsearchFilterCriteriaVisitor($this->getMapper());
        $criteria->acceptVisitor($criteriaVisitor);
        $filter = $criteriaVisitor->getArrayForCriteria($criteria);

        $query = array();
        $query['query'] = $filter;
        $query['aggs'] = $aggregation;

        $params = array();
        $params['index'] = $this->getIndexName();
        $params['body'] = $query;

        error_log(json_encode($params));

        $responseArray = $this->getClient()->search($params);

        return $responseArray['aggregations'];
    }

    public function aggregatePassThrough($criteria, $aggregation)
    {
        $criteriaVisitor = new ElasticsearchFilterCriteriaVisitor($this->getMapper());
        $criteria->acceptVisitor($criteriaVisitor);
        $filter = $criteriaVisitor->getArrayForCriteria($criteria);

        $query = array();
        $query['aggs'] = [
            'my_agg' => [
              'filter' => $filter,
              'aggs' => $aggregation,
            ],
        ];
        $params = array();
        $params['index'] = $this->getIndexName();
        $params['body'] = $query;

        error_log(json_encode($params));

        $responseArray = $this->getClient()->search($params);

        return $responseArray['aggregations']['my_agg'];
    }

    public function getColumnForField($field)
    {
        return $this->getMapper()->getColumnForField($field);
    }
}
