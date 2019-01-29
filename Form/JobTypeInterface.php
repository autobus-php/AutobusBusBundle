<?php

namespace Autobus\Bundle\BusBundle\Form;

use Symfony\Component\Form\FormTypeInterface;

/**
 * Interface JobTypeInterface
 * @package Autobus\Bundle\BusBundle\Form
 */
interface JobTypeInterface extends FormTypeInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool;
}
