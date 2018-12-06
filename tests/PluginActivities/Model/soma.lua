
-- Author  : Samuel T. C. Santos
-- version : 11.07.2014
-- Faça um Programa que peça dois números e imprima a soma.

while true do
    local number1, number2 = io.read("*number", "*number")
    if number1 == nil then break end 

    soma = number1 + number2
    print (soma)
end