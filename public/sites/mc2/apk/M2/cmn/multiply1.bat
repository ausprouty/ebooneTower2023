mkdir video
cd video
mkdir multiply1
cd ..
ffmpeg  -accurate_seek -i lumo/LUMOJohn10142.mp4 -ss 170 -to 229   -vf scale=480:-1  video/multiply1/101.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke15111631.mp4 -ss 0 -to 142   -vf scale=480:-1  video/multiply1/102.mp4
ffmpeg  -accurate_seek -i acts/PhilipandtheEthiopian.mp4 -ss 99 -to 161   -vf scale=480:-1  video/multiply1/104.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke24153.mp4 -ss 339 -to 475   -vf scale=480:-1  video/multiply1/105.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke11154.mp4 -ss 0 -to 139   -vf scale=480:-1  video/multiply1/106.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn1511615.mp4 -ss 0 -to 122   -vf scale=480:-1  video/multiply1/107.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke18943.mp4 -ss 258 -to 336   -vf scale=480:-1  video/multiply1/108.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke10142.mp4 -ss 0 -to 249   -vf scale=480:-1  video/multiply1/109.mp4
ffmpeg  -accurate_seek -i acts/PaulSpeakstotheCrowd.mp4 -ss 44 -to 295   -vf scale=480:-1  video/multiply1/110.mp4
ffmpeg  -accurate_seek -i acts/JesusTakenUpIntoHeaven.mp4 -ss 0 -to 103   -vf scale=480:-1  video/multiply1/111.mp4
ffmpeg  -accurate_seek -i acts/PeteratCornelius39sHouse.mp4 -ss 0 -to 230   -vf scale=480:-1  video/multiply1/112.mp4
ffmpeg  -accurate_seek -i acts/TheFellowshipoftheBelievers.mp4 -ss 0 -to 53   -vf scale=480:-1  video/multiply1/113.mp4
ffmpeg  -accurate_seek -i acts/TheSendingofBarnabasandSaul.mp4 -ss 11 -to 37   -vf scale=480:-1  video/multiply1/113-1.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke21372238.mp4 -ss 109 -to 197   -vf scale=480:-1  video/multiply1/114.mp4
ffmpeg  -accurate_seek -i acts/TheBelieversShareTheirPossessions.mp4 -ss 0 -to 50   -vf scale=480:-1  concat/115.mp4
ffmpeg  -accurate_seek -i acts/AnaniasandSapphira.mp4 -ss 0 -to 110   -vf scale=480:-1  concat/115-1.mp4
ffmpeg -f concat -safe 0 -i concat/multiply115.txt -c copy video/multiply1/115.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke1411510.mp4 -ss 245 -to 325   -vf scale=480:-1  video/multiply1/116.mp4
