Game = {}
Game.__index = Game

local monster_ = nil

function Game:createMonsterType(name)
   local monsterType = {}             	-- our new object
   setmetatable(monsterType,Game)  		-- make Game handle lookup
   monsterType.name = name      		-- initialize our object
   monsterType.register = function (self, monster)
   	monster_ = monster
   end

   return monsterType
end

function getMonster()
	return monster_
end
