<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Gestion des téléchargements
 */
class AmfjDownloadManager
{
    /**
     * @var bool Statut de la connexion
     */
    protected $connectionStatus;

    /**
     * @var string Token GitHub
     */
    protected $gitHubToken;
    
    /**
     * @var string
     */
    private $urlForTest = 'www.google.fr';
    
    public function getUrlForTest()
    {
        return $this->urlForTest;      
    }
    
    public function setUrlForTest($urlForTest)
    {
        $this->urlForTest = $urlForTest;
        return $this;
    }
    
    /**
     * Constructeur testant le statut de la connexion.
     */
    public function __construct($forceConnectionStatus = null)
    {
        if ($forceConnectionStatus !== null) {
            $this->connectionStatus = $forceConnectionStatus;
        }
        else {
            $this->connectionStatus = false;
            $this->testConnection();
        }
        $this->gitHubToken = config::byKey('github::token');
    }

    /**
     * Test le statut de la connexion.
     */
    protected function testConnection()
    {
        $sock = \fsockopen($this->getUrlForTest(), 80);
        if ($sock !== false) {
            $this->connectionStatus = true;
            fclose($sock);
        } else {
            $this->connectionStatus = $sock;
        }
    }

    /**
     * Obtenir le statut de la connexion
     *
     * @return bool True si la connexion fonctionne
     */
    public function isConnected()
    {
        return $this->connectionStatus;
    }

    /**
     * Télécharge un contenu à partir de son lien
     *
     * @param string $url Lien du contenu à télécharger.
     * @param bool $binary Télécharger un binaire
     *
     * @return string|bool Données téléchargées ou False en cas d'échec
     */
    public function downloadContent($url, $binary = false)
    {
        if ($this->gitHubToken !== false && $this->gitHubToken != '' && !$binary) {
            $toAdd = 'access_token=' . $this->gitHubToken;
            // Test si un paramètre a déjà été passé
            if (strpos($url, '?') !== false) {
                $url = $url . '&' . $toAdd;
            } else {
                $url = $url . '?' . $toAdd;
            }
        }
        log::add('AlternativeMarketForJeedom', 'debug', 'Download ' . $url);
        $result = false;
        if ($this->isCurlEnabled()) {
            $result = $this->downloadContentWithCurl($url, $binary);
        } elseif ($this->isUrlFopenEnabled()) {
            $result = $this->downloadContentWithFopen($url);
        }
        return $result;
    }

    /**
     * Télécharge un fichier binaire
     *
     * @param string $url Lien du fichier
     * @param string $dest Destination du fichier
     */
    public function downloadBinary($url, $dest)
    {
        $imgData = $this->downloadContent($url, true);
        if (\file_exists($dest)) {
            \unlink($dest);
        }
        $filePointer = \fopen($dest, 'wb');
        \fwrite($filePointer, $imgData);
        \fclose($filePointer);
    }

    /**
     * Test si la fonctionnalité cURL est activée
     *
     * @return bool True si la fonctionnalité est activée
     */
    protected function isCurlEnabled()
    {
        return function_exists('curl_version');
    }

    /**
     * Télécharge un contenu à partir de son lien avec la méthode cURL
     *
     * @param string $url Lien du contenu à télécharger.
     * @param bool $binary Télécharger un binaire
     *
     * @return string|bool Données téléchargées ou False en cas d'échec
     */
    protected function downloadContentWithCurl($url, $binary = false)
    {
        $content = false;
        $curlSession = curl_init();
        if ($curlSession !== false) {
            \curl_setopt($curlSession, CURLOPT_URL, $url);
            \curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
            if ($binary) {
                \curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            }
            \curl_setopt($curlSession, CURLOPT_USERAGENT, 'AlternativeMarketForJeedom');
            $content = \curl_exec($curlSession);
            \curl_close($curlSession);
        }
        return $content;
    }

    /**
     * Test si fopen peut être utilisé pour télécharger le contenu d'un lien
     *
     * @return bool True si c'est possible
     */
    protected function isUrlFopenEnabled()
    {
        return \ini_get('allow_fopen_url');
    }

    /**
     * Télécharge un contenu à partir de son lien avec la méthode fopen
     *
     * @param string $url Lien du contenu à télécharger.
     *
     * @return string|bool Données téléchargées ou False en cas d'échec
     */
    protected function downloadContentWithFopen($url)
    {
        $result = \file_get_contents($url);
        if ($result !== fasle){
             return $result;
        }  else {
            return $result;
        }
    }
}
