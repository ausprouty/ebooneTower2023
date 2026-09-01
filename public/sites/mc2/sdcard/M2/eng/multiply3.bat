mkdir video
cd video
mkdir multiply3
cd ..
ffmpeg  -accurate_seek -i acts/JesusTakenUpIntoHeaven.mp4 -ss 0  -vf scale=480:-1    video/multiply3/301.mp4
ffmpeg  -accurate_seek -i acts/MatthiasChosentoReplaceJudas.mp4 -ss 0  -vf scale=480:-1    video/multiply3/302.mp4
ffmpeg  -accurate_seek -i acts/TheHolySpiritComesatPentecost.mp4 -ss 0  -vf scale=480:-1    video/multiply3/303.mp4
ffmpeg  -accurate_seek -i acts/PeterAddressestheCrowd.mp4 -ss 0  -vf scale=480:-1    video/multiply3/304.mp4
ffmpeg  -accurate_seek -i acts/TheFellowshipoftheBelievers.mp4 -ss 0  -vf scale=480:-1    video/multiply3/305.mp4
ffmpeg  -accurate_seek -i acts/PeterHealsaLameBeggar.mp4 -ss 0 -to 127   -vf scale=480:-1  concat/306.mp4
ffmpeg  -accurate_seek -i acts/PeterSpeakstotheOnlookers.mp4 -ss 0  -vf scale=480:-1    concat/306-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply306.txt -c copy video/multiply3/306.mp4
ffmpeg  -accurate_seek -i acts/PeterandJohnbeforetheSanhedrin.mp4 -ss 0 -to 177   -vf scale=480:-1  concat/307.mp4
ffmpeg  -accurate_seek -i acts/TheBelieversPray.mp4 -ss 0  -vf scale=480:-1    concat/307-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply307.txt -c copy video/multiply3/307.mp4
ffmpeg  -accurate_seek -i acts/TheBelieversShareTheirPossessions.mp4 -ss 0 -to 47   -vf scale=480:-1  concat/308.mp4
ffmpeg  -accurate_seek -i acts/AnaniasandSapphira.mp4 -ss 0  -vf scale=480:-1    concat/308-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply308.txt -c copy video/multiply3/308.mp4
ffmpeg  -accurate_seek -i acts/TheApostlesHealMany.mp4 -ss 0 -to 64   -vf scale=480:-1  concat/309.mp4
ffmpeg  -accurate_seek -i acts/TheApostlesPersecuted.mp4 -ss 0  -vf scale=480:-1    concat/309-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply309.txt -c copy video/multiply3/309.mp4
ffmpeg  -accurate_seek -i acts/TheChoosingoftheSeven.mp4 -ss 0  -vf scale=480:-1    video/multiply3/310.mp4
ffmpeg  -accurate_seek -i acts/StephenSeized.mp4 -ss 0  -vf scale=480:-1    video/multiply3/311.mp4
ffmpeg  -accurate_seek -i acts/Stephen39sSpeechtotheSanhedrin.mp4 -ss 0  -vf scale=480:-1    video/multiply3/312.mp4
ffmpeg  -accurate_seek -i acts/StoningofStephen.mp4 -ss 0 -to 353   -vf scale=480:-1  concat/313.mp4
ffmpeg  -accurate_seek -i acts/TheChurchPersecutedandScattered.mp4 -ss 0  -vf scale=480:-1    concat/313-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply313.txt -c copy video/multiply3/313.mp4
ffmpeg  -accurate_seek -i acts/PhilipinSamaria.mp4 -ss 0 -to 25   -vf scale=480:-1  concat/314.mp4
ffmpeg  -accurate_seek -i acts/SimontheSorcerer.mp4 -ss 0  -vf scale=480:-1    concat/314-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply314.txt -c copy video/multiply3/314.mp4
ffmpeg  -accurate_seek -i acts/PhilipandtheEthiopian.mp4 -ss 0  -vf scale=480:-1    video/multiply3/315.mp4
ffmpeg  -accurate_seek -i acts/Saul39sConversion.mp4 -ss 0  -vf scale=480:-1    video/multiply3/316.mp4
ffmpeg  -accurate_seek -i acts/SaulinDamascusandJerusalem.mp4 -ss 0  -vf scale=480:-1    video/multiply3/317.mp4
ffmpeg  -accurate_seek -i acts/AeneasandDorcas.mp4 -ss 0  -vf scale=480:-1    video/multiply3/318.mp4
ffmpeg  -accurate_seek -i acts/CorneliusSeesanAngel.mp4 -ss 0 -to 71   -vf scale=480:-1  concat/319.mp4
ffmpeg  -accurate_seek -i acts/Peter39sVision.mp4 -ss 0 -to 110   -vf scale=480:-1  concat/319-1.mp4
ffmpeg  -accurate_seek -i acts/PeteratCornelius39sHouse.mp4 -ss 0 -to 95   -vf scale=480:-1  concat/319-2.mp4
ffmpeg -f concat -safe 0 -i concat/multiply319.txt -c copy video/multiply3/319.mp4
ffmpeg  -accurate_seek -i acts/PeteratCornelius39sHouse.mp4 -ss 95 -to 234   -vf scale=480:-1  video/multiply3/320.mp4
ffmpeg  -accurate_seek -i acts/PeterExplainsHisActions.mp4 -ss 0  -vf scale=480:-1    video/multiply3/321.mp4
ffmpeg  -accurate_seek -i acts/TheChurchinAntioch.mp4 -ss 0  -vf scale=480:-1    video/multiply3/322.mp4
ffmpeg  -accurate_seek -i acts/Peter39sMiraculousEscapeFromPrison.mp4 -ss 0 -to 294   -vf scale=480:-1  concat/323.mp4
ffmpeg  -accurate_seek -i acts/Herod39sDeath.mp4 -ss 0  -vf scale=480:-1    concat/323-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply323.txt -c copy video/multiply3/323.mp4
ffmpeg  -accurate_seek -i acts/TheSendingofBarnabasandSaul.mp4 -ss 11 -to 33   -vf scale=480:-1  concat/324.mp4
ffmpeg  -accurate_seek -i acts/MinistryOnCyprus.mp4 -ss 0  -vf scale=480:-1    concat/324-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply324.txt -c copy video/multiply3/324.mp4
ffmpeg  -accurate_seek -i acts/PaulsMinistryInPisidianAntioch.mp4 -ss 0 -to 341   -vf scale=480:-1  video/multiply3/325.mp4
ffmpeg  -accurate_seek -i acts/PaulsMinistryInPisidianAntioch.mp4 -ss 342 -to 413   -vf scale=480:-1  video/multiply3/326.mp4
ffmpeg  -accurate_seek -i acts/Paul&BarnabaspreachInIconium.mp4 -ss 0  -vf scale=480:-1    video/multiply3/327.mp4
ffmpeg  -accurate_seek -i acts/MistakenIdentityInLystra.mp4 -ss 0  -vf scale=480:-1    video/multiply3/328.mp4
ffmpeg  -accurate_seek -i acts/TheReturntoAntiochinSyria.mp4 -ss 0  -vf scale=480:-1    video/multiply3/329.mp4
ffmpeg  -accurate_seek -i acts/TheCouncilatJerusalem.mp4 -ss 0 -to 214   -vf scale=480:-1  concat/330.mp4
ffmpeg  -accurate_seek -i acts/TheCouncil39sLettertoGentileBelievers.mp4 -ss 0  -vf scale=480:-1    concat/330-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply330.txt -c copy video/multiply3/330.mp4
ffmpeg  -accurate_seek -i acts/DisagreementBetweenPaulandBarnabas.mp4 -ss 0 -to 45   -vf scale=480:-1  concat/331.mp4
ffmpeg  -accurate_seek -i acts/TimothyJoinsPaulandSilas.mp4 -ss 0  -vf scale=480:-1    concat/331-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply331.txt -c copy video/multiply3/331.mp4
ffmpeg  -accurate_seek -i acts/Paul39sVisionoftheManofMacedonia.mp4 -ss 0 -to 75   -vf scale=480:-1  concat/332.mp4
ffmpeg  -accurate_seek -i acts/Lydia39sConversioninPhilippi.mp4 -ss 0  -vf scale=480:-1    concat/332-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply332.txt -c copy video/multiply3/332.mp4
ffmpeg  -accurate_seek -i acts/PaulandSilasinPrison.mp4 -ss 0  -vf scale=480:-1    video/multiply3/333.mp4
ffmpeg  -accurate_seek -i acts/RiotsinThessalonica.mp4 -ss 0 -to 88   -vf scale=480:-1  concat/334.mp4
ffmpeg  -accurate_seek -i acts/TheNobleBereans.mp4 -ss 0  -vf scale=480:-1    concat/334-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply334.txt -c copy video/multiply3/334.mp4
ffmpeg  -accurate_seek -i acts/IdolsinAthens.mp4 -ss 0  -vf scale=480:-1    video/multiply3/335.mp4
ffmpeg  -accurate_seek -i acts/DivisioninCorinth.mp4 -ss 0 -to 194   -vf scale=480:-1  concat/336.mp4
ffmpeg  -accurate_seek -i acts/PriscillaAquilaandApollos.mp4 -ss 0 -to 48   -vf scale=480:-1  concat/336-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply336.txt -c copy video/multiply3/336.mp4
ffmpeg  -accurate_seek -i acts/PriscillaAquilaandApollos.mp4 -ss 48 -to 106   -vf scale=480:-1  video/multiply3/337.mp4
ffmpeg  -accurate_seek -i acts/PaulinEphesus.mp4 -ss 0 -to 74   -vf scale=480:-1  video/multiply3/338.mp4
ffmpeg  -accurate_seek -i acts/PaulinEphesus.mp4 -ss 74 -to 191   -vf scale=480:-1  video/multiply3/339.mp4
ffmpeg  -accurate_seek -i acts/TheRiotinEphesus.mp4 -ss 0  -vf scale=480:-1    video/multiply3/340.mp4
ffmpeg  -accurate_seek -i acts/ThroughMacedoniaandGreece.mp4 -ss 0 -to 138   -vf scale=480:-1  concat/341.mp4
ffmpeg  -accurate_seek -i acts/EutychusRaisedFromtheDeadatTroas.mp4 -ss 0  -vf scale=480:-1    concat/341-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply341.txt -c copy video/multiply3/341.mp4
ffmpeg  -accurate_seek -i acts/Paul39sFarewelltotheEphesianElders.mp4 -ss 0  -vf scale=480:-1    video/multiply3/342.mp4
ffmpeg  -accurate_seek -i acts/OntoJerusalem.mp4 -ss 0 -to 144   -vf scale=480:-1  concat/343.mp4
ffmpeg  -accurate_seek -i acts/Paul39sArrivalatJerusalem.mp4 -ss 0  -vf scale=480:-1    concat/343-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply343.txt -c copy video/multiply3/343.mp4
ffmpeg  -accurate_seek -i acts/PaulArrested.mp4 -ss 0  -vf scale=480:-1    video/multiply3/344.mp4
ffmpeg  -accurate_seek -i acts/PaulSpeakstotheCrowd.mp4 -ss 0  -vf scale=480:-1    video/multiply3/345.mp4
ffmpeg  -accurate_seek -i acts/PaultheRomanCitizen.mp4 -ss 0 -to 98   -vf scale=480:-1  concat/346.mp4
ffmpeg  -accurate_seek -i acts/PaulbeforetheSanhedrin.mp4 -ss 0  -vf scale=480:-1    concat/346-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply346.txt -c copy video/multiply3/346.mp4
ffmpeg  -accurate_seek -i acts/ThePlottoKillPaul.mp4 -ss 26 -to 134   -vf scale=480:-1  concat/347.mp4
ffmpeg  -accurate_seek -i acts/PaulTransferredtoCaesarea.mp4 -ss 0  -vf scale=480:-1    concat/347-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply347.txt -c copy video/multiply3/347.mp4
ffmpeg  -accurate_seek -i acts/Paul39sTrialBeforeFelix.mp4 -ss 0  -vf scale=480:-1    video/multiply3/348.mp4
ffmpeg  -accurate_seek -i acts/Paul39sTrialBeforeFestus.mp4 -ss 0 -to 273   -vf scale=480:-1  concat/349.mp4
ffmpeg  -accurate_seek -i acts/FestusConsultsKingAgrippa.mp4 -ss 0  -vf scale=480:-1    concat/349-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply349.txt -c copy video/multiply3/349.mp4
ffmpeg  -accurate_seek -i acts/PaulbeforeAgrippa.mp4 -ss 0  -vf scale=480:-1    video/multiply3/350.mp4
ffmpeg  -accurate_seek -i acts/PaulSailsforRome.mp4 -ss 0 -to 124   -vf scale=480:-1  concat/351.mp4
ffmpeg  -accurate_seek -i acts/TheStorm.mp4 -ss 0  -vf scale=480:-1    concat/351-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply351.txt -c copy video/multiply3/351.mp4
ffmpeg  -accurate_seek -i acts/TheShipwreck.mp4 -ss 0  -vf scale=480:-1    video/multiply3/352.mp4
ffmpeg  -accurate_seek -i acts/PaulAshoreonMalta.mp4 -ss 0  -vf scale=480:-1    video/multiply3/353.mp4
ffmpeg  -accurate_seek -i acts/PaulFinallyReachesRome.mp4 -ss 0 -to 55   -vf scale=480:-1  concat/354.mp4
ffmpeg  -accurate_seek -i acts/PaulPreachesinRomeUnderGuard.mp4 -ss 0  -vf scale=480:-1    concat/354-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply354.txt -c copy video/multiply3/354.mp4
