<?php

namespace App\Application\Infrastructure\Persistence\JWT;

use JWT\Authentication\JWT;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * NixillaJWTEncoder
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class NixillaJWTEncoder implements JWTEncoderInterface
{
    private $key;
    private string|int|bool|array|null|float|\UnitEnum $inerval;
    private string|int|bool|array|null|float|\UnitEnum $interval;
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->key = $parameterBag->get('secret_key');
        $this->interval = $parameterBag->get('jwt_token_ttl');
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        $data['exp'] = time() + $this->interval;
        try {
            return JWT::encode($data, $this->key);
        } catch (\Exception $e) {
            throw new JWTEncodeFailureException(JWTEncodeFailureException::INVALID_CONFIG, 'An error occurred while trying to encode the JWT token.', $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            return (array) JWT::decode($token, $this->key);
        } catch (\Exception $e) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Invalid JWT Token', $e);
        }
    }
}
