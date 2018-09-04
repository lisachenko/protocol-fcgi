<?php declare(strict_types=1);

namespace Lisachenko\Protocol\FCGI\Record;

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\Record;

/**
 * The Web server sends a FCGI_BEGIN_REQUEST record to start a request.
 *
 * @author Alexander.Lisachenko
 */
class BeginRequest extends Record
{

    /**
     * The role component sets the role the Web server expects the application to play.
     * The currently-defined roles are:
     *   FCGI_RESPONDER
     *   FCGI_AUTHORIZER
     *   FCGI_FILTER
     *
     * @var int
     */
    protected $role = FCGI::UNKNOWN_ROLE;

    /**
     * The flags component contains a bit that controls connection shutdown.
     *
     * flags & FCGI_KEEP_CONN:
     *   If zero, the application closes the connection after responding to this request.
     *   If not zero, the application does not close the connection after responding to this request;
     *   the Web server retains responsibility for the connection.
     *
     * @var int
     */
    protected $flags;

    /**
     * Reserved data, 5 bytes maximum
     *
     * @var string
     */
    protected $reserved1;

    public function __construct(int $role = FCGI::UNKNOWN_ROLE, int $flags = 0, string $reserved = '')
    {
        $this->type = FCGI::BEGIN_REQUEST;
        $this->role = $role;
        $this->flags = $flags;
        $this->reserved1 = $reserved;
        $this->setContentData($this->packPayload());
    }

    /**
     * Returns the role
     *
     * The role component sets the role the Web server expects the application to play.
     * The currently-defined roles are:
     *   FCGI_RESPONDER
     *   FCGI_AUTHORIZER
     *   FCGI_FILTER
     */
    public function getRole(): int
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

    /**
     * {@inheritdoc}
     * @param static $self
     */
    protected static function unpackPayload($self, string $data): void
    {
        [
            $self->role,
            $self->flags,
            $self->reserved1
        ] = array_values(unpack("nrole/Cflags/a5reserved", $data));
    }

    /** {@inheritdoc} */
    protected function packPayload(): string
    {
        return pack(
            "nCa5",
            $this->role,
            $this->flags,
            $this->reserved1
        );
    }
}
