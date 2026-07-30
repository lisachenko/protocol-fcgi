<?php
/*
 * Protocol FCGI library
 *
 * @copyright Copyright 2021. Lisachenko Alexander <lisachenko.it@gmail.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use Lisachenko\Protocol\FCGI\ProtocolException;
use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\RecordType;
use Lisachenko\Protocol\FCGI\Role;

/**
 * The Web server sends a FCGI_BEGIN_REQUEST record to start a request.
 */
final class BeginRequest extends Record
{
    /**
     * @param Role   $role      The role the Web server expects the application to play
     * @param int    $flags     Bit mask controlling connection shutdown, see FCGI::KEEP_CONN
     * @param string $reserved1 Reserved data, 5 bytes maximum
     */
    public function __construct(
        protected Role $role,
        protected int $flags = 0,
        protected string $reserved1 = '',
    ) {
        $this->type = RecordType::BeginRequest;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns the role the Web server expects the application to play
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * Returns the flags
     *
     * The flags component contains a bit that controls connection shutdown.
     *
     * flags & FCGI_KEEP_CONN:
     *   If zero, the application closes the connection after responding to this request.
     *   If not zero, the application does not close the connection after responding to this request;
     *   the Web server retains responsibility for the connection.
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    protected function unpackPayload(string $binaryData): void
    {
        /** @var false|array{role: int, flags: int, reserved: string} $payload */
        $payload = unpack("nrole/Cflags/a5reserved", $binaryData);
        if ($payload === false) {
            throw new ProtocolException('Can not unpack the FCGI_BeginRequestBody');
        }

        $this->role      = Role::tryFrom($payload['role'])
            ?? throw new ProtocolException("Invalid FastCGI role {$payload['role']} received");
        $this->flags     = $payload['flags'];
        $this->reserved1 = $payload['reserved'];
    }

    protected function packPayload(): string
    {
        return pack(
            "nCa5",
            $this->role->value,
            $this->flags,
            $this->reserved1
        );
    }
}
