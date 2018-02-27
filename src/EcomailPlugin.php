<?php

declare(strict_types=1);

namespace Czende\EcomailPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jan Czernin <jan.czernin@gmail.com>
 */
final class EcomailPlugin extends Bundle
{
    use SyliusPluginTrait;
}