<?php

namespace Model;

use Skautis\Skautis;

/**
 * @author Hána František <sinacek@gmail.com>
 */
class AuthService
{

    /** @var Skautis */
    private $skautis;


    public function __construct(Skautis $skautis)
    {
        $this->skautis = $skautis;
    }

    /**
     * vrací přihlašovací url
     * @param string $backlink
     */
    public function getLoginUrl($backlink): string
    {
        return $this->skautis->getLoginUrl($backlink);
    }

    /**
     * nastavuje základní udaje po prihlášení do SkautISu
     */
    public function setInit(string $token, int $roleId, int $unitId): void
    {
        $this->skautis->getUser()->setLoginData($token, $roleId, $unitId);
    }

    /**
     * vrací url pro odhlášení
     */
    public function getLogoutUrl(): string
    {
        return $this->skautis->getLogoutUrl();
    }

}
