# List of emails for registered people
select distinct email 
from person
left join person_plan on person_plan.person_id = person.id
where person_plan.project_key = 'AYSOS5Games2013';

# Distinct list of physical teams
select distinct level.age, level.sex, game_team.name
from game
left join project on project.id = game.project_id
left join game_team on game_team.game_id = game.id
left join level on level.id = game_team.level_id
where project.hash = 'AYSOS5Games2013' and game.pool = 'PP'
order by level.age, level.sex, game_team.name
;

# Slots assigned so far
select count(*)
from game_person
left join game on game.id = game_person.game_id
left join project on project.id = game.project_id
where project.hash = 'AYSOS5Games2013' and game_person.person_id IS NOT NULL
;
# Total slots
select count(*)
from game_person
left join game on game.id = game_person.game_id
left join project on project.id = game.project_id
where project.hash = 'AYSOS5Games2013'
;

# Referees for a given game
select game.id,game.num,game.dt_beg,game_person.role,game_person.name,person.email
from game_person
left join game on game.id = game_person.game_id
left join project on project.id = game.project_id
left join person on person.id = game_person.person_id
where project.hash = 'AYSOS5Games2013' and game.num = 165
;

# delete a game
delete from game_person
