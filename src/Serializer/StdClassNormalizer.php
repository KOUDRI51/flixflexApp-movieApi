<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class StdClassNormalizer implements NormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \stdClass;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        // Convert the stdClass object to an array
        return (array) $object;
    }
}