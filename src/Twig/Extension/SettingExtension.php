<?php

namespace Drupal\wmsettings\Twig\Extension;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\wmsettings\Service\WmSettings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingExtension extends AbstractExtension
{
    /** @var RendererInterface */
    protected $renderer;
    /** @var WmSettings */
    protected $wmSettings;

    /** @var array */
    protected $cache;

    public function __construct(
        RendererInterface $renderer,
        WmSettings $wmSettings
    ) {
        $this->renderer = $renderer;
        $this->wmSettings = $wmSettings;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setting', [$this, 'getSetting']),
        ];
    }

    public function getSetting($bundle)
    {
        if (!is_string($bundle)) {
            return null;
        }

        if (!isset($this->cache[$bundle])) {
            $this->cache[$bundle] = $this->wmSettings->read($bundle);
        }

        $entity = $this->cache[$bundle];

        // Workaround to include caching metadata of the settings entity
        if ($entity instanceof EntityInterface) {
            $build = [];
            CacheableMetadata::createFromObject($entity)
                ->applyTo($build);
            $this->renderer->render($build);
        }

        return $entity;
    }
}
