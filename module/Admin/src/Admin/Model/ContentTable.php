<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;

final class ContentTable
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Doctrine\ORM\QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @return Admin\Entity\Content
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Content");
    }

    /**
     * @param int $id content id
     * @param int $language user language
     *
     * @throws RuntimeException If content is not found
     *
     * @return Content
     */
    public function getContent($id = 0, $language = 1)
    {
        $content = $this->queryBuilder();
        $content->select(["c"]);
        $content->from('Admin\Entity\Content', 'c');
        $content->where("c.id = :id AND c.language = :language");
        $content->setParameter(':id', (int) $id);
        $content->setParameter(':language', (int) $language);
        $content = $content->getQuery()->getSingleResult();

        if (empty($content)) {
            throw new RuntimeException("Couldn't find content");
        }

        return $content;
    }

    /**
     * Delete content based on the provided id and language.
     *
     * @param int $id content id
     * @param int $language user language
     *
     * @return Content
     */
    public function deleteContent($id = 0, $language = 1)
    {
        if ($this->getContent($id, $language)) {
            $del = $this->queryBuilder();
            $del->delete('Admin\Entity\Content', 'c');
            $del->where("c.id = :id AND c.language = :language");
            $del->setParameter(':id', (int) $id);
            $del->setParameter(':language', (int) $language);

            return $del->getQuery()->execute();
        }
    }

    /**
     * Save or update content based on the provided id and language.
     *
     * @param  Content $content
     *
     * @return Content
     */
    public function saveContent(Content $content)
    {
        $data = [
            'menu'      => (int) $content->getMenu(),
            'title'     => (string) $content->getTitle(),
            'preview'   => (string) $content->getPreview(),
            'text'      => (string) $content->getText(),
            'menuOrder' => (int) $content->getMenuOrder(),
            'type'      => (int) $content->getType(),
            'date'      => (string) $content->getDate(),
            'language'  => (int) $content->getLanguage(),
            'titleLink' => (string) $content->getTitleLink(),
            'active'    => (int) $content->getActive(),
            'author'    => (string) $content->getAuthor(),
        ];
        $id = (int) $content->getId();
        $language = (int) $content->getLanguage();
        if (!$id) {
            $this->insert($data);
        } else {
            if ($this->getContent($id, $language)) {
                $this->update($data, ['id' => $id, 'language' => $language]);
            }
        }
        unset($id, $language, $data);
        return $content;
    }

    /**
     * This method can disable or enable contents.
     *
     * @param int $id content id
     * @param  int $state 0 - deactivated, 1 - active
     */
    public function toggleActiveContent($id = 0, $state = 0)
    {
        if ($this->getContent($id)) {
            $this->update(["active" => (int) $state], ['id' => (int) $id]);
        }
    }

    /**
     * duplicate a content.
     *
     * @param  int    $id
     * @param  int    $language
     *
     * @return Content
     */
    public function duplicate($id = 0, $language = 1)
    {
        $content = $this->getContent($id, $language);
        $clone = $content->getCopy();
        $this->saveContent($clone);
        return $clone;
    }
}
