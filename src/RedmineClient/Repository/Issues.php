<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Issues as IssuesTrait;
use Librinfo\RedmineComponent\Entity\Issue;

class Issues extends Generic
{
    use IssuesTrait;

    protected function getEntityClass(): string
    {
        return Issue::class;
    }

    public function updateStatus(Issue $issue, $newStatusId)
    {
        $data = [
          'issue' => [
            'id'        => $issue->get('id'),
            'status_id' => $newStatusId,
          ]
        ];
        $this->getClient()->setMethod('PUT');
        $this->getClient()->setRoute($this->getRoute() . '/' . $issue->get('id'));

        return $this->getClient()->sendRequest([
        'json' => $data,
      ]);
    }

    public function updateOrder(Issue $issue, $newOrder)
    {
        $data = [
          'issue' => [
            'id'        => $issue->get('id'),
            'custom_fields' => [['id'=>7, "value" => $newOrder]],
          ]
        ];
        $this->getClient()->setMethod('PUT');
        $this->getClient()->setRoute($this->getRoute() . '/' . $issue->get('id'));

        return $this->getClient()->sendRequest([
        'json' => $data,
      ]);
    }
}
