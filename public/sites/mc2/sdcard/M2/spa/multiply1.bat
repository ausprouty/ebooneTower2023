mkdir video
cd video
mkdir multiply1
cd ..
ffmpeg  -accurate_seek -i lumo/LUMOJohn10142.mp4 -ss 170 -to 229   -vf scale=480:-1  video/multiply1/101.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke15111631.mp4 -ss 0 -to 144   -vf scale=480:-1  video/multiply1/102.mp4
ffmpeg  -accurate_seek -i acts/PhilipandtheEthiopian.mp4 -ss 99 -to 161   -vf scale=480:-1  video/multiply1/104.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke24153.mp4 -ss 339 -to 475   -vf scale=480:-1  video/multiply1/105.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke11154.mp4 -ss 0 -to 141   -vf scale=480:-1  video/multiply1/106.mp4
ffmpeg  -accurate_seek -i lumo/LUMOJohn1511615.mp4 -ss 0 -to 122   -vf scale=480:-1  video/multiply1/107.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke18943.mp4 -ss 260 -to 336   -vf scale=480:-1  video/multiply1/108.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke5139.mp4 -ss 370 -to 445   -vf scale=480:-1  video/multiply1/109.mp4
ffmpeg  -accurate_seek -i acts/PaulSpeakstotheCrowd.mp4 -ss 45 -to 296   -vf scale=480:-1  video/multiply1/110.mp4
ffmpeg  -accurate_seek -i acts/JesusTakenUpIntoHeaven.mp4 -ss 0 -to 103   -vf scale=480:-1  video/multiply1/111.mp4
ffmpeg  -accurate_seek -i acts/PeteratCornelius39sHouse.mp4 -ss 0 -to 108   -vf scale=480:-1  video/multiply1/112.mp4
ffmpeg  -accurate_seek -i acts/TheFellowshipoftheBelievers.mp4 -ss 0  -vf scale=480:-1    video/multiply1/113.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke21372238.mp4 -ss 109 -to 196   -vf scale=480:-1  video/multiply1/114.mp4
ffmpeg  -accurate_seek -i acts/TheBelieversShareTheirPossessions.mp4 -ss 0  -vf scale=480:-1    video/multiply1/115.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke1411510.mp4 -ss 245 -to 325   -vf scale=480:-1  video/multiply1/116.mp4
