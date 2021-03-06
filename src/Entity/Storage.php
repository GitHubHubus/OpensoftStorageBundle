<?php

namespace Opensoft\StorageBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opensoft\StorageBundle\Storage\Adapter\AwsS3AdapterConfiguration;
use Opensoft\StorageBundle\Storage\Adapter\LocalAdapterConfiguration;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity(repositoryClass="Opensoft\StorageBundle\Entity\Repository\StorageRepository")
 * @ORM\Table(name="storage")
 */
class Storage
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(type="array", name="adapter_options")
     */
    private $adapterOptions = [];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @var ArrayCollection|StorageFile[]
     *
     * @ORM\OneToMany(targetEntity="Opensoft\StorageBundle\Entity\StorageFile", mappedBy="storage")
     */
    private $files;

    /**
     *
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|StorageFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAdapterType(): ?string
    {
        if (!isset($this->adapterOptions['class'])) {
            return null;
        }

        $class = $this->adapterOptions['class'];

        // BC shim to support new namespaces while extracting storage engine code into bundle
        if ($class == 'Opensoft\Onp\Bundle\CoreBundle\Storage\Adapter\LocalAdapterConfiguration') {
            $class = LocalAdapterConfiguration::class;
        } elseif ($class == 'Opensoft\Onp\Bundle\CoreBundle\Storage\Adapter\AwsS3AdapterConfiguration') {
            $class = AwsS3AdapterConfiguration::class;
        }

        return $class::getName();
    }

    /**
     * @return array
     */
    public function getAdapterOptions(): ?array
    {
        return $this->adapterOptions;
    }

    /**
     * @param array $adapterOptions
     */
    public function setAdapterOptions(array $adapterOptions): void
    {
        $this->adapterOptions = $adapterOptions;
    }

    /**
     * @return bool
     */
    public function isLocal(): bool
    {
        if (!isset($this->adapterOptions['class'])) {
            return false;
        }

        $class = $this->adapterOptions['class'];
        // BC shim to support new namespaces while extracting storage engine code into bundle
        if ($class == 'Opensoft\Onp\Bundle\CoreBundle\Storage\Adapter\LocalAdapterConfiguration') {
            $class = LocalAdapterConfiguration::class;
        } elseif ($class == 'Opensoft\Onp\Bundle\CoreBundle\Storage\Adapter\AwsS3AdapterConfiguration') {
            $class = AwsS3AdapterConfiguration::class;
        }

        return $class === LocalAdapterConfiguration::class;
    }

    /**
     * @throws \BadMethodCallException
     * @return string
     */
    public function getLocalPath(): string
    {
        if (!$this->isLocal()) {
            throw new \BadMethodCallException('Local paths may not be retrieved from remote storage');
        }

        return $this->adapterOptions['directory'];
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
