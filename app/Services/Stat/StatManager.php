<?php

namespace App\Services\Stat;

use App\Models\Character\Character;
use App\Models\Level\CharacterLevel;
use App\Models\Level\UserLevel;
use App\Models\Stat\Stat;
use App\Models\User\User;
use App\Services\Service;
use Auth;
use Carbon\Carbon;
use DB;

class StatManager extends Service {
    /**
     * Gives user stat points to use on characters (from levelling up).
     *
     * @param object $user
     * @param mixed  $type
     * @param mixed  $data
     * @param mixed  $next
     */
    public function creditUserStat($user, $type, $data, $next) {
        DB::beginTransaction();

        try {
            $points = $next->stat_points;

            if ($this->createTransferLog($user->id, 'User', $user->id, 'User', $type, $data, $points)) {
                $user->level->current_points += $points;
                $user->level->save();
            } else {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Transfers stat points from a user to a character from the user's built up points.
     *
     * @param mixed $user
     * @param mixed $character
     * @param mixed $quantity
     */
    public function userToCharacter($user, $character, $quantity) {
        DB::beginTransaction();

        try {
            if ($user->id != $character->user_id) {
                throw new \Exception('You must own the character to transfer to it.');
            }
            if ($user->level->current_points < $quantity) {
                throw new \Exception('Not enough points to transfer this amount.');
            }

            $recipient_stack = $character->level;
            $stack = $user->level;
            if (!$recipient_stack) {
                throw new \Exception('This character has no level log.');
            }

            $stack->current_points -= $quantity;
            $recipient_stack->current_points += $quantity;
            $stack->save();
            $recipient_stack->save();

            $type = 'User Transfer';
            $data = $user->displayName.' transferred '.$quantity.' exp to '.$character->displayName;

            if ($type && !$this->createTransferLog($user->id, $user->logType, $character->id, $character->logType, $type, $data, $quantity)) {
                throw new \Exception('Failed to create log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /* --------------------------------
    |
    |   CHARACTER
    |
    |  -----------------------------------
    */

    /*
    *   Level the stat
    */
    public function levelCharaStat($stat, $character, $isStaff = false) {
        DB::beginTransaction();

        try {
            // registering previous
            $previous = $stat->stat_level;
            $stat->stat_level += 1;
            $stat->save();

            $headerStat = $stat->stat;
            if ($headerStat->multiplier || $headerStat->step) {
                // FIN
                // First if there's a step, add that
                // This is so that the multiplier affects the new step total
                // E.G if the current is 10 and step is 5, we do 15 * multiplier
                // This can be changed if desired but generally I think this is fine
                if ($headerStat->step) {
                    $stat->count += $headerStat->step;
                    $stat->save();
                }
                if ($headerStat->multiplier) {
                    $total = $stat->count * $headerStat->multiplier;
                    //if($total < 0) $total = $total * -1;
                    $stat->count = $total;
                    $stat->save();
                }
            }
            if (!$isStaff) {
                if ($character->level->current_points < 1) {
                    throw new \Exception('You do not have enough stat points to level');
                }
                $character->level->current_points -= 1;
                $character->level->save();
            }

            $type = 'Stat Level Up';
            $data = 'Point used in stat level up.';
            if (!$this->createTransferLog($character->id, 'Character', $character->id, 'Character', $type, $data, -1)) {
                throw new \Exception('Error creating log.');
            }
            if (!$this->createLevelLog($character->id, $headerStat->id, 'Character', $previous, $stat->stat_level)) {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /*
    * Credit stat
    */
    public function creditCharaStat($chara, $type, $data, $next) {
        DB::beginTransaction();

        try {
            $points = $next->stat_points;

            if ($this->createTransferLog($chara->id, 'Character', $chara->id, 'Character', $type, $data, $points)) {
                $chara->level->current_points += $points;
                $chara->level->save();
            } else {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * sets the stat directly instead of incrementing or decrementing.
     *
     * @param mixed $stat
     * @param mixed $character
     * @param mixed $quantity
     */
    public function setCharaStat($stat, $character, $quantity) {
        DB::beginTransaction();

        try {
            $sender = Auth::user();
            if (!$sender->isStaff) {
                throw new \Exception('You are not staff.');
            }

            $stat->current_count = $quantity;
            $stat->save();

            $type = 'Staff Edit';
            $data = 'Edited Current Count by Staff';

            if (!$this->createCountLog($sender->id, $sender->logtype, $character, $type, $data, $quantity, $stat->id)) {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /*
    * Edit stat
    * aka the current stat amount, increment or decrement
    */
    public function editCharaStat($stat, $character, $quantity, $override = false) {
        DB::beginTransaction();

        try {
            $sender = Auth::user();
            if (!$sender->isStaff && !$override) {
                throw new \Exception('You are not staff.');
            }

            if ($stat->current_count == null) {
                $stat->current_count = $stat->count;
                $stat->save();
            } else {
                $stat->current_count += $quantity;
                $stat->save();
            }

            if ($stat->current_count < 0) {
                $stat->current_count = 0;
                $stat->save();
            }

            if ($stat->current_count > $stat->count) {
                $stat->current_count = $stat->count;
                $stat->save();
            }

            if ($override) {
                // if different logs are needed
            } else {
                $type = 'Staff Edit';
                $data = 'Edited Current Count by Staff';
            }

            if (!$this->createCountLog($sender->id, $sender->logtype, $character, $type, $data, $quantity, $stat->id)) {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * edit base stat (aka what the current stat is based on).
     *
     * @param mixed $stat
     * @param mixed $character
     * @param mixed $quantity
     */
    public function editBaseCharaStat($stat, $character, $quantity) {
        DB::beginTransaction();

        try {
            $sender = Auth::user();
            if (!$sender->isStaff) {
                throw new \Exception('You are not staff.');
            }

            $stat->count = $quantity;
            $stat->save();

            $type = 'Staff Edit';
            $data = 'Editted Base Count by Staff';

            if (!$this->createCountLog($sender->id, $sender->logtype, $character, $type, $data, $quantity, $stat->id)) {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sets a stat to a specific value. (for potions and the like).
     *
     * @param mixed $sender
     * @param mixed $stat
     * @param mixed $quantity
     * @param mixed $type
     * @param mixed $data
     */
    public function setStat($sender, $stat, $quantity, $type, $data) {
        DB::beginTransaction();

        try {
            $sender = Auth::user();

            if ($quantity > $stat->count) {
                $quantity = $stat->count;
            }

            $stat->current_count = $quantity;
            $stat->save();

            if (!$this->createCountLog($sender->id, $sender->logtype, $stat->character, $type, $data, $quantity)) {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    public function regenStat($stat, $character, $quantity, $type, $data) {
        DB::beginTransaction();

        try {
            if ($stat->current_count == null) {
                $stat->current_count = $stat->count;
                $stat->save();
            } else {
                $stat->current_count += $quantity;
                $stat->save();
            }

            if ($stat->current_count < 0) {
                $stat->current_count = 0;
                $stat->save();
            }

            if ($stat->current_count > $stat->count) {
                $stat->current_count = $stat->count;
                $stat->save();
            }

            if (!$this->createCountLog(null, 'User', $character, $type, $data, $quantity, $stat->id)) {
                throw new \Exception('Error creating log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /*-----------------------------------------------
    *
    * MISC
    *
    *-----------------------------------------------/

    /**
     * Grants / transfers Stat points (to level up) to one user or character
     *
     */
    public function creditStat($sender, $recipient, $type, $data, $quantity) {
        DB::beginTransaction();

        try {
            // for user
            if ($recipient->logType == 'User') {
                $recipient_stack = UserLevel::where('user_id', '=', $recipient->id)->first();

                if (!$recipient_stack) {
                    $recipient_stack = UserLevel::create(['user_id' => $recipient->id]);
                }
                $recipient_stack->current_points += $quantity;
                $recipient_stack->save();
            }
            // for character
            else {
                $recipient_stack = CharacterLevel::where('character_id', $recipient->id)->first();

                if (!$recipient_stack) {
                    $recipient_stack = CharacterLevel::create(['character_id' => $recipient->id]);
                }
                $recipient_stack->current_points += $quantity;
                $recipient_stack->save();
            }
            if ($type && !$this->createTransferLog($sender ? $sender->id : null, $sender ? $sender->logType : null, $recipient ? $recipient->id : null, $recipient ? $recipient->logType : null, $type, $data, $quantity)) {
                throw new \Exception('Failed to create log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * debit Stat points.
     *
     * @param mixed $sender
     * @param mixed $recipient
     * @param mixed $type
     * @param mixed $data
     * @param mixed $quantity
     */
    public function debitStat($sender, $recipient, $type, $data, $quantity) {
        DB::beginTransaction();

        try {
            // for user
            if ($sender->logType == 'User') {
                $sender_stack = UserLevel::where('user_id', '=', $sender->id)->first();

                if (!$sender_stack) {
                    $sender_stack = UserLevel::create(['user_id' => $sender->id]);
                }
                if ($sender_stack->current_points < $quantity) {
                    throw new \Exception('Not enough points to debit.');
                }
                $sender_stack->current_points -= $quantity;
                $sender_stack->save();
            }
            // for character
            else {
                $sender_stack = CharaLevels::where('character_id', $sender->id)->first();

                if (!$sender_stack) {
                    $sender_stack = CharaLevels::create(['character_id' => $sender->id]);
                }
                if ($sender_stack->current_points < $quantity) {
                    throw new \Exception('Not enough points to debit.');
                }
                $sender_stack->current_points -= $quantity;
                $sender_stack->save();
            }
            if ($type && !$this->createTransferLog($sender ? $sender->id : null, $sender ? $sender->logType : null, $recipient ? $recipient->id : null, $recipient ? $recipient->logType : null, $type, $data, $quantity * -1)) {
                throw new \Exception('Failed to create log.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a log.
     *
     * @param mixed $senderId
     * @param mixed $senderType
     * @param mixed $recipientId
     * @param mixed $recipientType
     * @param mixed $type
     * @param mixed $data
     * @param mixed $quantity
     */
    public function createTransferLog($senderId, $senderType, $recipientId, $recipientType, $type, $data, $quantity) {
        return DB::table('stat_transfer_log')->insert(
            [
                'sender_id'      => $senderId,
                'sender_type'    => $senderType,
                'recipient_id'   => $recipientId,
                'recipient_type' => $recipientType,
                'log'            => $type.($data ? ' ('.$data.')' : ''),
                'log_type'       => $type,
                'data'           => $data,
                'quantity'       => $quantity,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]
        );
    }

    /**
     * Creates a log.
     *
     * @param mixed $recipientId
     * @param mixed $stat
     * @param mixed $recipientType
     * @param mixed $previous
     * @param mixed $new
     */
    public function createLevelLog($recipientId, $stat, $recipientType, $previous, $new) {
        return DB::table('stat_log')->insert(
            [
                'recipient_id'   => $recipientId,
                'stat_id'        => $stat,
                'leveller_type'  => $recipientType,
                'previous_level' => $previous,
                'new_level'      => $new,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]
        );
    }

    /**
     * Creates a log.
     *
     * @param mixed      $senderId
     * @param mixed      $senderType
     * @param mixed      $character
     * @param mixed      $type
     * @param mixed      $data
     * @param mixed      $quantity
     * @param mixed|null $stat_id
     */
    public function createCountLog($senderId, $senderType, $character, $type, $data, $quantity, $stat_id = null) {
        return DB::table('count_log')->insert(
            [
                'sender_id'    => $senderId ?? null,
                'sender_type'  => $senderType,
                'character_id' => $character->id,
                'log'          => $type.($data ? ' ('.$data.')' : ''),
                'log_type'     => $type,
                'data'         => $data,
                'quantity'     => $quantity,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
                'stat_id'      => $stat_id ?? null,
            ]
        );
    }
}
