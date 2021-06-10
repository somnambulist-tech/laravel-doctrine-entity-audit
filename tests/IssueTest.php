<?php
/*
 * (c) 2011 SimpleThings GmbH
 *
 * @package SimpleThings\EntityAudit
 * @author Benjamin Eberlei <eberlei@simplethings.de>
 * @author Andrew Tch <andrew.tchircoff@gmail.com>
 * @link http://www.simplethings.de
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

namespace Somnambulist\EntityAudit\Tests;

use Somnambulist\EntityAudit\Exception\NoRevisionFoundException;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\DuplicateRevisionFailureTestOwnedElement;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\DuplicateRevisionFailureTestPrimaryOwner;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\DuplicateRevisionFailureTestSecondaryOwner;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\EscapedColumnsEntity;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue156Client;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue156Contact;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue156ContactTelephoneNumber;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue31Reve;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue31User;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue87AbstractProject;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue87Organization;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue87Project;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue87ProjectComment;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue9Address;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\Issue9Customer;
use Somnambulist\EntityAudit\Tests\Fixtures\Issue\IssueReservedSQLKeywordsAsColumnNames;

class IssueTest extends BaseTest
{
    protected $schemaEntities = [
        EscapedColumnsEntity::class,
        Issue87Project::class,
        Issue87ProjectComment::class,
        Issue87AbstractProject::class,
        Issue87Organization::class,
        Issue9Address::class,
        Issue9Customer::class,
        Issue87Organization::class,
        DuplicateRevisionFailureTestPrimaryOwner::class,
        DuplicateRevisionFailureTestSecondaryOwner::class,
        DuplicateRevisionFailureTestOwnedElement::class,
        Issue31User::class,
        Issue31Reve::class,
        Issue156Contact::class,
        Issue156ContactTelephoneNumber::class,
        Issue156Client::class,
        IssueReservedSQLKeywordsAsColumnNames::class,
    ];

    protected $auditedEntities = [
        EscapedColumnsEntity::class,
        Issue87Project::class,
        Issue87ProjectComment::class,
        Issue87AbstractProject::class,
        Issue87Organization::class,
        Issue9Address::class,
        Issue9Customer::class,
        Issue87Organization::class,
        DuplicateRevisionFailureTestPrimaryOwner::class,
        DuplicateRevisionFailureTestSecondaryOwner::class,
        DuplicateRevisionFailureTestOwnedElement::class,
        Issue31User::class,
        Issue31Reve::class,
        Issue156Contact::class,
        Issue156ContactTelephoneNumber::class,
        Issue156Client::class,
        IssueReservedSQLKeywordsAsColumnNames::class,
    ];

    public function testIssue31()
    {
        $reve = new Issue31Reve();
        $reve->setTitre('reve');

        $this->em->persist($reve);
        $this->em->flush();

        $user = new Issue31User();
        $user->setTitre('user');
        $user->setReve($reve);

        $this->em->persist($user);
        $this->em->remove($reve);
        $this->em->flush();

        $reader = $this->auditManager->getAuditReader();

        $this->expectException(NoRevisionFoundException::class);
        $reader->find(get_class($reve), $reve->getId(), 1);
    }

    public function testEscapedColumns()
    {
        $e = new EscapedColumnsEntity();
        $e->setLeft(1);
        $e->setLft(2);
        $this->em->persist($e);
        $this->em->flush();

        $reader = $this->auditManager->getAuditReader();

        $result = $reader->find(get_class($e), $e->getId(), 1);

        $this->assertInstanceOf(EscapedColumnsEntity::class, $result);
    }

    public function testIssue87()
    {
        $org     = new Issue87Organization();
        $project = new Issue87Project();
        $project->setOrganisation($org);
        $project->setSomeProperty('some property');
        $project->setTitle('test project');
        $comment = new Issue87ProjectComment();
        $comment->setProject($project);
        $comment->setText('text comment');

        $this->em->persist($org);
        $this->em->persist($project);
        $this->em->persist($comment);
        $this->em->flush();

        $auditReader = $this->auditManager->getAuditReader();

        $auditedProject = $auditReader->find(get_class($project), $project->getId(), 1);

        $this->assertEquals($org->getId(), $auditedProject->getOrganisation()->getId());
        $this->assertEquals('test project', $auditedProject->getTitle());
        $this->assertEquals('some property', $auditedProject->getSomeProperty());

        $auditedComment = $auditReader->find(get_class($comment), $comment->getId(), 1);
        $this->assertEquals('test project', $auditedComment->getProject()->getTitle());

        $project->setTitle('changed project title');
        $this->em->flush();

        $auditedComment = $auditReader->find(get_class($comment), $comment->getId(), 2);
        $this->assertEquals('changed project title', $auditedComment->getProject()->getTitle());

    }

    public function testIssue9()
    {
        $address = new Issue9Address();
        $address->setAddressText('NY, Red Street 6');

        $customer = new Issue9Customer();
        $customer->setAddresses([$address]);
        $customer->setPrimaryAddress($address);

        $address->setCustomer($customer);

        $this->em->persist($customer);
        $this->em->persist($address);

        $this->em->flush(); //#1

        $reader = $this->auditManager->getAuditReader();

        $aAddress = $reader->find(get_class($address), $address->getId(), 1);
        $this->assertEquals($customer->getId(), $aAddress->getCustomer()->getId());

        /** @var Issue9Customer $aCustomer */
        $aCustomer = $reader->find(get_class($customer), $customer->getId(), 1);

        $this->assertNotNull($aCustomer->getPrimaryAddress());
        $this->assertEquals('NY, Red Street 6', $aCustomer->getPrimaryAddress()->getAddressText());
    }

    public function testDuplicateRevisionKeyConstraintFailure()
    {
        $primaryOwner = new DuplicateRevisionFailureTestPrimaryOwner();
        $this->em->persist($primaryOwner);

        $secondaryOwner = new DuplicateRevisionFailureTestSecondaryOwner();
        $this->em->persist($secondaryOwner);

        $primaryOwner->addSecondaryOwner($secondaryOwner);

        $element = new DuplicateRevisionFailureTestOwnedElement();
        $this->em->persist($element);

        $primaryOwner->addElement($element);
        $secondaryOwner->addElement($element);

        $this->em->flush();

        $this->em->getUnitOfWork()->clear();

        $primaryOwner = $this->em->find('Somnambulist\EntityAudit\Tests\Fixtures\Issue\DuplicateRevisionFailureTestPrimaryOwner', 1);

        $this->em->remove($primaryOwner);
        $this->em->flush();

        $this->expectException(NoRevisionFoundException::class);

        $reader = $this->auditManager->getAuditReader();
        $reader->find(get_class($primaryOwner), $primaryOwner->getId(), 1);
    }

    public function testIssue156()
    {
        $client = new Issue156Client();

        $number = new Issue156ContactTelephoneNumber();
        $number->setNumber('0123567890');
        $client->addTelephoneNumber($number);

        $this->em->persist($client);
        $this->em->persist($number);
        $this->em->flush();

        $auditReader = $this->auditManager->getAuditReader();
        $object      = $auditReader->find(get_class($number), $number->getId(), 1);

        $this->assertInstanceOf(get_class($number), $object);
    }

    public function testIssueReservedSQLKeywordsAsColumnNames()
    {
        $test = new IssueReservedSQLKeywordsAsColumnNames();
        $test->setDefault('some default')->setOrder(23);

        $this->em->persist($test);
        $this->em->flush();

        $auditReader = $this->auditManager->getAuditReader();
        $object      = $auditReader->find(get_class($test), $test->getId(), 1);

        $this->assertInstanceOf(get_class($test), $object);
    }
}
