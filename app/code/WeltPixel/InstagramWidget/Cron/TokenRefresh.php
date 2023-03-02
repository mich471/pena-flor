<?php
namespace WeltPixel\InstagramWidget\Cron;

/**
 * Class TokenRefresh
 * @package WeltPixel\InstagramWidget\Cron
 */
class TokenRefresh
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_configWriter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * Tokens constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        $this->_serializer = $serializer;
        $this->_scopeConfig = $scopeConfig;
        $this->_configWriter = $configWriter;
        $this->_dateTime = $dateTime;
    }


    /**  */
    public function execute()
    {
        $tokenOptions = $this->_scopeConfig->getValue(\WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\InstagramToken::TOKEN_PATH);
        if (isset($tokenOptions)) {
            $tokenGenerationInformation = '<p>Last Token Regeneration Triggered at: <b>' .  $this->_dateTime->gmtDate(). '</b>' ;
            $tokenGenerationInformation .= '<p>--------------------------------------------------------------------------------------------</p>';
            $tokens = $this->_serializer->unserialize($tokenOptions);
            foreach ($tokens as &$tokenOpt) {
                $oldToken = $tokenOpt['token_value'];
                $newToken = $this->_generateToken($oldToken);
                if (is_array($newToken)) {
                    $tokenOpt['token_value'] = $newToken['token'];
                    $tokenGenerationInformation .= '<p> Token <b>' . $tokenOpt['token_name'] . '</b> regenerated successfully and expires around: <b>' .  $this->_dateTime->gmtDate('Y-m-d h:i:s', strtotime(' +' .   $newToken['expires_in'] . ' seconds')) . '</b>.</p>';
                } else {
                    $tokenGenerationInformation .= '<p> Token <b>' . $tokenOpt['token_name'] . '</b> was not regenerated.' . '</p>';
                }
            }
            $tokenOptions = $this->_serializer->serialize($tokens);
            $this->_configWriter->save(\WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\InstagramToken::TOKEN_PATH, $tokenOptions);
            $this->_configWriter->save(\WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\InstagramToken::TOKEN_INFO_PATH, $tokenGenerationInformation);
        }
    }


    /**
     * @param string $oldToken
     * @return false|string
     */
    protected function _generateToken($oldToken)
    {
        $urlEndpoint = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $oldToken;
        try {
            $ch = curl_init($urlEndpoint);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $result = curl_exec($ch);
            $response = json_decode($result, true);
            if (isset($response['access_token'])) {
                return [
                    'token' => $response['access_token'],
                    'expires_in' => $response['expires_in']
                ];
            }
            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
