<?php
namespace Cerad\Bundle\JanrainBundle\Security;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\HttpFoundation\Request;

/* ==================================================================
 * This is basically a striped down version of FormAuthenticationEntryPoint
 * But without the userForward option
 * Think I copied it from HWI?
 */
class JanrainAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var Symfony\Component\Security\Http\HttpUtils
     */
    private $httpUtils;

    /**
     * @var string
     */
    private $loginPath;

    /**
     * Constructor
     *
     * @param HttpUtils              $httpUtils
     * @param string                 $loginPath
     */
    public function __construct(HttpUtils $httpUtils, $loginPath)
    {
        $this->httpUtils = $httpUtils;
        $this->loginPath = $loginPath;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
    }
}
