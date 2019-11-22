<?php namespace CodesPhp\Feriados;

use GuzzleHttp\Client;
use CodesPhp\Support\Arr;
use CodesPhp\Support\Attrs;
use CodesPhp\Support\Collection;

class Feriados
{
    /**
     * Token de acesso.
     * @var string|null
     */
    protected $token;

    /**
     * Client request.
     * @var Client
     */
    protected $client;

    /**
     * string 
     */
    protected $endpoint = 'https://api.calendario.com.br/?json=true';

    /**
     * Contrutor class.
     * 
     * @param string $token
     */
    public function __construct($token = null)
    {
        $this->token = $token;        

        $this->client = new Client([]);
    }

    /**
     * Request base.
     * 
     * @param array $params
     * @return mixed
     */
    protected function request($params = [])
    {
        $uri = $this->makeUri($params);

        $respose = $this->client->request('get', $uri);
        $json = json_decode(trim($respose->getBody()), true);

        return $json;
    }

    /**
     * Get feriado pelo ano estado e municipio.
     * 
     * @param string $ano
     * @param string $uf
     * @param string $municipio
     * @return Attrs|null
     */
    public function getByEstadoMunicipio($ano, $uf, $municipio)
    {
        $uf        = strtoupper($uf);
        $municipio = strtoupper($municipio);

        $data = $this->request(['ano' => $ano, 'estado' => $uf, 'cidade' => $municipio]);
        if (is_array($data)) {
            return Collection::make(array_map(function($item) {
                return Feriado::make($item);
            }, $data));
        }

        return null;
    }

    /**
     * Get feriado pelo ano codigo ibge
     * 
     * @param string $ano
     * @param string $codIbge
     * @return Attrs|null
     */
    public function getByCodigoIBGE($ano, $codIbge)
    {
        $data = $this->request(['ano' => $ano, 'ibge' => $codIbge]);
        if (is_array($data)) {
            return Collection::make(array_map(function($item) {
                return Feriado::make($item);
            }, $data));
        }

        return null;
    }

    /**
     * Make URI.
     * 
     * @param array $params
     * @return string
     */
    protected function makeUri($params = [])
    {
        $uri = $this->endpoint;

        // Check Token
        if (! is_null($this->token)) {
            $params['token'] = $this->token;
        }

        // Params
        foreach ($params as $k => $v) {
            $uri .= 
            $uri .= '&' . $k  . '=' . urlencode($v);
        }

        return $uri;
    }
}