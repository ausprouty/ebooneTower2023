mkdir video
cd video
mkdir hope
cd ..
ffmpeg  -accurate_seek -i lumo/LUMOLuke7150.mp4 -ss 390 -to 569   -vf scale=480:-1  video/hope/01.mp4
ffmpeg  -accurate_seek -i youtube/zxBiv7jk4Zs.mp4 -ss 0  -vf scale=480:-1    video/hope/01-100.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke18943.mp4 -ss 0 -to 108   -vf scale=480:-1  video/hope/02.mp4
ffmpeg  -accurate_seek -i youtube/n4EnWVOcWpU.mp4 -ss 0  -vf scale=480:-1    video/hope/02-100.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19144.mp4 -ss 0 -to 81   -vf scale=480:-1  video/hope/03.mp4
ffmpeg  -accurate_seek -i youtube/gOmiN_Lnqzk.mp4 -ss 0  -vf scale=480:-1    video/hope/03-100.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew18101930.mp4 -ss 108 -to 235   -vf scale=480:-1  video/hope/04.mp4
ffmpeg  -accurate_seek -i youtube/3doLFxX49ZQ.mp4 -ss 0  -vf scale=480:-1    video/hope/04-100.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke22392325.mp4 -ss 280 -to 552   -vf scale=480:-1  video/hope/05.mp4
ffmpeg  -accurate_seek -i youtube/f91qmWKSsjo.mp4 -ss 0  -vf scale=480:-1    video/hope/05-100.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke24153.mp4 -ss 0 -to 171   -vf scale=480:-1  video/hope/06.mp4
ffmpeg  -accurate_seek -i youtube/Yy2AECMIp38.mp4 -ss 0  -vf scale=480:-1    video/hope/06-100.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke15111631.mp4 -ss 0 -to 225   -vf scale=480:-1  video/hope/07.mp4
ffmpeg  -accurate_seek -i youtube/xItcassfEaY.mp4 -ss 0  -vf scale=480:-1    video/hope/07-100.mp4
