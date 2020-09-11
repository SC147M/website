<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class AdminController extends EasyAdminController
{
    protected function persistEntity($entity)
    {
        if (method_exists($entity, 'setUser')) {
            if (empty($entity->getUser())) {
                $user = $this->getUser();
                $entity->setUser($user);
            }
        }

        if (method_exists($entity, 'setViews')) {
            if (empty($entity->getViews())) {
                $entity->setViews(0);
            }
        }

        parent::persistEntity($entity);
    }
}