 # Coins and EXP

Coins are virtual currency. Users earn coins playing games and engaging on the site. Users may also purchase coins.

EXP is experience earns for engaging on the site.

Note both coins and EXP are earned based on server-side logic and authenticated user transactions. They are never
awarded on the client.

| Event                | Coins earned | EXP earned |
|----------------------|--------------|------------|
| Confirm registration | 20           | 1          |
| Play a game the first time |  1     | 1          |
| Submit a game score  | 0            | 1          |
| Share a game (each returning unique user) | 1            | 1          |
| Earn #1 on a leaderboard | 1        | 1          |

## Share

1 coin earned with each unique friend and returned (how to do this?)

- each share request generates a token hash{site_id, user_id, game_id, email-address, salt}. note how this works one-time per unique user_id/email combination.
- add to token tracking tracking table
- shared user must return with that token for requesting user to get credit
- all done on server, part of Send To Friend function
- on return check token against usage table, if not used then award coins.

## Earning on in-game events

How do games setup earning based on in-game events?

- score threshold
- achievements
- game-defined

 *        - 1 coin if score > 15,000
 *        - 1 coin if level >= 10 (Stewie), and/or WIN
 *        - earn 1 coin when unlock all achievements (requires achievements defined on server)
