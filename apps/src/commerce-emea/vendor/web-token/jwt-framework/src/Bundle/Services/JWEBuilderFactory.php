<?php

declare(strict_types=1);

namespace Jose\Bundle\JoseFramework\Services;

use Jose\Component\Core\AlgorithmManagerFactory;
use Jose\Component\Encryption\Compression\CompressionMethodManagerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;

final class JWEBuilderFactory
{
    public function __construct(
        private readonly AlgorithmManagerFactory $algorithmManagerFactory,
        private readonly CompressionMethodManagerFactory $compressionMethodManagerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * This method creates a JWEBuilder using the given algorithm aliases.
     *
     * @param string[] $keyEncryptionAlgorithms
     * @param string[] $contentEncryptionAlgorithms
     * @param string[] $compressionMethods
     */
    public function create(
        array $keyEncryptionAlgorithms,
        array $contentEncryptionAlgorithms,
        array $compressionMethods
    ): JWEBuilder {
        $algorithmManager = $this->algorithmManagerFactory->create(
            array_merge($keyEncryptionAlgorithms, $contentEncryptionAlgorithms)
        );
        $compressionMethodManager = $this->compressionMethodManagerFactory->create($compressionMethods);

        return new JWEBuilder($algorithmManager, null, $compressionMethodManager, $this->eventDispatcher);
    }
}
