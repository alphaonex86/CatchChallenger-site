<?php
/**
 * Class V32
 * @see https://github.com/Cyan4973/xxHash/wiki/xxHash-specification-(draft)
 */
class V32
{
    private $seed = 0;

    public function __construct(int $seed = 0)
    {
        $this->seed = $seed;
    }

    public function hash(string $input): string
    {
        $file = tmpfile();
        fwrite($file, $input);
        rewind($file);

        if (!isset($this)) {
            $hash = new self();
            return $hash->hashStream($file);
        }

        return $this->hashStream($file);
    }

    public function hashStream($input): string
    {
        if (!isset ($this)) {
            $hash = new self();
            return $hash->hashStream($input);
        }

        if (get_resource_type($input) !== 'stream') {
            throw new InvalidArgumentException();
        }

        $accumulators = [
            $this->add(
                $this->add($this->seed, 2654435761),
                2246822519
            ),
            $this->add($this->seed, 2246822519),
            $this->seed,
            $this->minus($this->seed, 2654435761),
        ];


        $accumulator = $this->add($this->seed, 374761393);
        $length = 0;
        while (($part = fread($input, 16)) && ($length += strlen($part)) && strlen($part) == 16) {
            foreach ([0,1,2,3] as $iteration) {
                $lane = $this->getLane($part, ($iteration * 4));

                $current = &$accumulators[$iteration];
                $current = $this->add(
                    $current,
                    $this->multiply($lane, 2246822519)
                );
                $current = $this->leftShift($current, 13);
                $current = $this->multiply($current, 2654435761);
            }
        }

        if ($length >= 16) {
            $accumulator = $this->accumulate($accumulators);
        }
        $accumulator = $this->add($accumulator, $length);
        return $this->smallInput($part, $accumulator);
    }

    private function smallInput($input, $accumulator): string
    {

        $a = $this->handle4ByteGroups($input, $accumulator);
        $accumulator=$a[0];
        $i=$a[1];
        $length=$a[2];

        $accumulator = $this->handleLeftOverBytes($input, $accumulator, $length, $i);

        $accumulator = $this->finalise($accumulator);

        return dechex($accumulator);
    }

    /*
     * Below are the maths functions described in the documentation
     * These try to emulate a real 32 bit unsigned int
     */


    /**
     * 32 bit safe multiplication
     */
    public function multiply($a, $b):int
    {
        $result = $a * $b;
        if (is_int($result) && $result > 0) {
            return $result % (1<<32);
        }

        //fall back to bc if the result overflows
        //todo: Try and not rely on an extension
        $result = bcmul((string) $a, (string) $b);
        return bcmod($result, 1<<32);
    }

    /**
     * Add but wrap at 32 bits
     */
    public function add($a, $b):int
    {
        return ($a + $b) % (1<<32);
    }

    /**
     * function to emulate an unsigned minus
     */
    public function minus($a, $b):int
    {
        $result = $a-$b;
        if ($result > 0) {
            return $result;
        }

        return $result & ((1<<32) -1);
    }

    /**
     * left shift wrap at 32 bits
     */
    public function leftShift($x, $bits): int
    {
        //left shift is a circular shift so shifted off bits become lower bits
        $circle = $x >> (32-$bits);
        return (($x << $bits) | $circle) % (1<<32);
    }

    /**
     * This function is to get the little endian byte
     */
    private function getLane($input, $i): int
    {
        return
            (ord($input[$i + 3]) << 24) +
            (ord($input[$i + 2]) << 16) +
            (ord($input[$i + 1]) << 8) +
            (ord($input[$i]))
        ;
    }

    /**
     * @param $accumulator
     * @return int
     */
    private function finalise($accumulator): int
    {
        $accumulator ^= ($accumulator >> 15);
        $accumulator = $this->multiply($accumulator, 2246822519);
        $accumulator ^= ($accumulator >> 13);
        $accumulator = $this->multiply($accumulator, 3266489917);
        $accumulator ^= ($accumulator >> 16);
        return $accumulator;
    }

    /**
     * @param $input
     * @param $accumulator
     * @param $length
     * @param $i
     * @return int
     */
    private function handleLeftOverBytes($input, $accumulator, $length, $i): int
    {
        while ($length >= 1) {
            $lane = ord($input[$i]);
            $accumulator = $this->add(
                $accumulator,
                $this->multiply($lane, 374761393)
            );
            $accumulator = $this->multiply(
                $this->leftShift($accumulator, 11),
                2654435761
            );
            $i += 1;
            $length -= 1;
        }
        return $accumulator;
    }

    /**
     * @param $input
     * @param $accumulator
     * @return array
     */
    private function handle4ByteGroups($input, $accumulator): array
    {
        $length = strlen($input);
        $i = 0;
        while ($length >= 4) {
            $lane = $this->getLane($input, $i);

            $accumulator = $this->add(
                $accumulator,
                $this->multiply($lane, 3266489917)
            );

            $accumulator = $this->multiply(
                $this->leftShift($accumulator, 17),
                668265263
            );

            $i += 4;
            $length -= 4;
        }
        return array($accumulator, $i, $length);
    }

    /**
     * @param $accumulators
     * @return int
     */
    private function accumulate($accumulators): int
    {
        $accumulator =
            $this->add(
                $this->add(
                    $this->leftShift($accumulators[0], 1),
                    $this->leftShift($accumulators[1], 7)
                ),
                $this->add(
                    $this->leftShift($accumulators[2], 12),
                    $this->leftShift($accumulators[3], 18)
                )
            );
        return $accumulator;
    }
}
