<?php

namespace ApiVideo\Client\Exception;

use InvalidArgumentException;

final class GenericClientException extends InvalidArgumentException implements ClientExceptionInterface
{
}
