<?php

namespace App\Controller;

use App\Entity\Follower;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Repository\FavoriteRepository;
use App\Repository\FollowerRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Upload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AvatarType;
use App\Form\BannerType;
use App\Repository\UploadRepository;

class ProfileController extends AbstractController
{
    /**
     * @Route("/{username}", name="profile_view")
     */
    public function view(User $user, UserRepository $repository,
                         UploadRepository $uploadRepository)
    {
        if ($user === $this->getUser())
        {
            $upload = new Upload();
            $formAvatar = $this->createForm(AvatarType::class, $upload);
            $formBanner = $this->createForm(BannerType::class, $upload);
        }

        $arrayUploads = $uploadRepository->getImages($user);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'formAvatar' => isset($formAvatar) ? $formAvatar->createView() : null,
            'formBanner' => isset($formBanner) ? $formBanner->createView() : null,
            'images' => $arrayUploads
        ]);
    }

    /**
     * @Route("/uploadAvatar", name="uploadAvatar")
     */
    public function uploadAvatar(Request $request, ObjectManager $em)
    {
        $user = $this->getUser();
        $upload = new Upload();
        $upload->setUploader($user);
        $formAvatar = $this->createForm(AvatarType::class, $upload);
        $formAvatar->handleRequest($request);
        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            foreach ($user->getUploads() as $key => $item)
                if ($item->getType() === "avatar") {
                    $upload = $user->getUploads()[$key];
                    $lastPic = $this->getParameter('Avatar_directory') .
                        '/' . $upload->getPath();
                    unlink($lastPic);
                }
            $file = $formAvatar['path']->getData();
            $filename = md5(random_bytes(20));
            $file->move($this->getParameter('Avatar_directory'),
                $filename);
            $upload->setPath($filename);
            $upload->setType('avatar');
            $user->addUpload($upload);
            $em->persist($upload);
            $em->flush();
        }
        return $this->redirectToRoute("profile_view", [
            "username" => $user->getUsername()
        ]);
    }

    /**
     * @Route("/uploadBanner", name="uploadBanner")
     */
    public function uploadBanner(Request $request, ObjectManager $em)
    {
        $user = $this->getUser();
        $upload = new Upload();
        $upload->setUploader($user);
        $formBanner = $this->createForm(BannerType::class, $upload);
        $formBanner->handleRequest($request);
        if ($formBanner->isSubmitted() && $formBanner->isValid())
        {
            foreach ($user->getUploads() as $key => $item)
                if ($item->getType() === "banner")
                {
                    $upload = $user->getUploads()[$key];
                    $lastPic = $this->getParameter('Banner_directory').
                        '/'.$upload->getPath();
                    unlink($lastPic);
                }
            $file = $formBanner['path']->getData();
            $filename = md5(random_bytes(20));
            $file->move($this->getParameter('Banner_directory'),
                $filename);
            $upload->setPath($filename);
            $upload->setType('banner');
            $user->addUpload($upload);
            $em->persist($upload);
            $em->flush();
        }
        return $this->redirectToRoute('profile_view', [
            "username" => $user->getUsername()
        ]);
    }

    /**
     * @Route("/{username}/following", name="profile_following")
     */
    public function following(User $user, UserRepository $userRepository,
                              FollowerRepository $repository,
                              UploadRepository $uploadRepository)
    {
        if ($user === $this->getUser())
        {
            $upload = new Upload();
            $formAvatar = $this->createForm(AvatarType::class, $upload);
            $formBanner = $this->createForm(BannerType::class, $upload);
        }

        $arrayUploads = $uploadRepository->getImages($user);
        return $this->render('profile/following.html.twig', [
            'user' => $user,
            'images' => $arrayUploads,
            'formAvatar' => isset($formAvatar) ? $formAvatar->createView() : null,
            'formBanner' => isset($formBanner) ? $formBanner->createView() : null
        ]);
    }

    /**
     * @Route("/{username}/followers", name="profile_followers")
     */
    public function followers(User $user, UserRepository $userRepository,
                              FollowerRepository $repository,
                              UploadRepository $uploadRepository)
    {
        if ($user === $this->getUser())
        {
            $upload = new Upload();
            $formAvatar = $this->createForm(AvatarType::class, $upload);
            $formBanner = $this->createForm(BannerType::class, $upload);
        }

        $arrayUploads = $uploadRepository->getImages($user);
        return $this->render('profile/followers.html.twig', [
            'user' => $user,
            'images' => $arrayUploads,
            'formAvatar' => isset($formAvatar) ? $formAvatar->createView() : null,
            'formBanner' => isset($formBanner) ? $formBanner->createView() : null
        ]);
    }

    /**
     * @Route("/{username}/likes", name="profile_favorites")
     */
    public function favorites(User $user, UserRepository $userRepository,
                              FavoriteRepository $repository,
                              UploadRepository $uploadRepository)
    {
        if ($user === $this->getUser())
        {
            $upload = new Upload();
            $formAvatar = $this->createForm(AvatarType::class, $upload);
            $formBanner = $this->createForm(BannerType::class, $upload);
        }

        $arrayUploads = $uploadRepository->getImages($user);
        return $this->render('profile/favorites.html.twig', [
            'user' => $user,
            'images' => $arrayUploads,
            'formAvatar' => isset($formAvatar) ? $formAvatar->createView() : null,
            'formBanner' => isset($formBanner) ? $formBanner->createView() : null
        ]);
    }

    /**
     * @Route("/{username}/edit", name="edit_profile")
     */
    public function edit_profile(Request $request, ObjectManager $manager)

    {
        $edit_profile = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $edit_profile);

        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){
            $manager->persist($edit_profile);
            $manager->flush();

            return $this->redirectToRoute('profile_view', ["username" =>
                $edit_profile->getUsername()]);
        }
        return $this->render('profile/edit.html.twig', [
            'formEdit' => $form->createView()
        ]);
    }
    /**
     * @Route("/{username}/follow", name="follow_ajax")
     */
    public function follow(User $otherUser, ObjectManager $manager,
                           FollowerRepository $followerRepository){
        $user = $this->getUser();
        $otherUser->getId();
        $unfollower = $followerRepository->findOneBy(['follower' => $user,
            'user'=> $otherUser]);

        if ($otherUser != $user && $unfollower === null){
            $follower = new Follower();
            $follower->setUser($otherUser);
            $follower->setFollower($user);
            $follower->setFollowDate(new \DateTime());

            $otherUser->addFollower($follower);

            $manager->persist($follower);
            $manager->flush();

            return new JsonResponse(["success"=>true]);
        }
        else{
            return new JsonResponse(["success"=>false]);
        }
    }

    /**
     * @Route("/{username}/unfollow", name="unfollow_ajax")
     */
    public function unfollow(User $otherUser, ObjectManager $manager, FollowerRepository $followerRepository){
        $user = $this->getUser();
        $otherUser->getId();

        $unfollower = $followerRepository->findOneBy(['follower' => $user, 'user'=> $otherUser]);

        $manager->remove($unfollower);
        $manager->flush();

        return new JsonResponse([]);
    }
}
