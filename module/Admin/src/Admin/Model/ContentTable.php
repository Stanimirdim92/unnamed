<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;
use Admin\Entity\Content;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

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
     * @param Query|QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param boolean            $fetchJoinCollection Whether the query joins a collection (true by default).
     *
     * @return Paginator
     */
    public function preparePagination($query, $fetchJoinCollection = true)
    {
        return new ZendPaginator(new PaginatorAdapter(new ORMPaginator($query, $fetchJoinCollection)));
    }

    /**
     * @return Admin\Entity\User
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\User");
    }

    /**
     * @param int $contentId content id
     * @param int $language user language
     *
     * @throws RuntimeException If content is not found
     *
     * @return Content
     */
    public function getContent($contentId = 0, $language = 1)
    {
        $content = $this->queryBuilder();
        $content->select(["c"]);
        $content->from('Admin\Entity\Content', 'c');
        $content->where("c.id = :id AND c.language = :language");
        $content->setParameter(':id', (int) $contentId);
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
     * @param int $contentId content id
     * @param int $language user language
     */
    public function deleteContent($contentId = 0, $language = 1)
    {
        $content = $this->getContent($contentId, $language);
        if ($content) {
            $this->entityManager->remove($content);
            $this->entityManager->flush();
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
        $this->entityManager->persist($content);
        $this->entityManager->flush();

        return $content;
    }

    /**
     * This method can disable or enable contents.
     *
     * @param int $contentId content id
     * @param int $language user language
     * @param  int $state 0 - deactivated, 1 - active
     */
    public function toggleActiveContent($contentId = 0,  $language = 1, $state = 0)
    {
        $menu = $this->getContent($contentId, $language);

        if ($menu) {
            $menu->setActive((int) $state);
            $this->saveContent($menu);
        }
    }
}
