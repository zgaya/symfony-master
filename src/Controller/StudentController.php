<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Student;
use App\Form\StudentType;

class StudentController extends AbstractController {
    
    #[Route('/student', name: 'list_students')]
    public function listStudents() {
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        return $this->render('student/list_students.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/student/add', name: 'add_students')]
    public function addStudent(Request $request) {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('list_students');
        }

        return $this->render('student/add_students.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/student/edit/{id}', name: 'edit_student')]
    public function editStudent(Request $request, $id) {
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            throw $this->createNotFoundException('Student not found');
        }

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('list_students');
        }

        return $this->render('student/edit_students.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/student/remove/{id}', name: 'remove_student')]
    public function removeStudent($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            throw $this->createNotFoundException('Student not found');
        }

        $entityManager->remove($student);
        $entityManager->flush();

        return $this->redirectToRoute('list_students');
    }
}
