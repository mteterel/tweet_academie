<?php

namespace App\Twig;

use App\Entity\ChatConversation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /** @var UserRepository $userRepository */
    private $userRepository;
    private $sfRouter;
    private $tokenStorage;

    public function __construct(RouterInterface $router, UserRepository $userRepository, TokenStorageInterface $tsi)
    {
        $this->sfRouter = $router;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tsi;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('format_post', [$this, 'format_post'])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_chat_title', [$this, 'get_chat_title'])
        ];
    }

    public function get_chat_title(ChatConversation $conversation)
    {
        $current_username = $this->tokenStorage->getToken()->getUsername();
        $participants = $conversation->getParticipants();
        $participants = array_filter($participants->toArray(), function($elem) use ($current_username) {
            /** @var User $elem */
            return $elem->getUsername() !== $current_username;
        });

        $participants_cnt = count($participants);
        $string = 'Conversation with ';

        $i = 0;
        foreach($participants as $participant)
        {
            if ($participant->getUsername() == $current_username)
                continue;
            $string .= $participant->getUsername();
            if ($i < $participants_cnt - 1)
                $string .= ', ';
            $i++;
        }

        return $string;
    }

    public function format_post(string $postContent)
    {
        $postContent = htmlspecialchars($postContent);
        $this->format_url($postContent);
        $this->format_hashtags($postContent);
        $this->format_user_mention($postContent);

        return $postContent;
    }

    public function format_url(string &$str)
    {
        $str = preg_replace(
            '/(http|ftp|https):\/\/([\w_-]+(?:(?:\.[\w_-]+)+))([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])?/',
            "<a href='$0'>$0</a>",
            $str);
    }

    public function format_hashtags(string &$str)
    {
        $str = preg_replace(
            "/#([A-Za-z0-9]\w+)/",
            "<a href='/hashtag/$1'>#$1</a>",
            $str);
    }

    public function format_user_mention(string &$str)
    {
        $matches = [];
        $match_cnt = preg_match_all("/@([A-Za-z0-9\-_]+)/",
            $str,
            $matches);

        if ($match_cnt <= 0)
            return;

        foreach ($matches[0] as $i => $match)
        {
            $user = $this->userRepository->findOneBy([
                'username' => $matches[1][$i]
            ]);

            if ($user !== null)
            {
                $url = $this->sfRouter->generate('profile_view', [
                    'username' => $matches[1][$i]
                ]);

                $str = str_replace(
                    $match,
                    "<a href='" . $url . "'>$match</a>",
                    $str);
            }
        }
    }
}