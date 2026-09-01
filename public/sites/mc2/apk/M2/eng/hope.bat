mkdir video
cd video
mkdir hope
cd ..
ffmpeg  -accurate_seek -i lumo/LUMOLuke7150.mp4 -ss 387 -to 566   -vf scale=480:-1  video/hope/01.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke18943.mp4 -ss 0 -to 108   -vf scale=480:-1  video/hope/02.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19144.mp4 -ss 0 -to 81   -vf scale=480:-1  video/hope/03.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew18101930.mp4 -ss 107 -to 232   -vf scale=480:-1  video/hope/04.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke22392325.mp4 -ss 278 -to 551   -vf scale=480:-1  video/hope/05.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke24153.mp4 -ss 0 -to 171   -vf scale=480:-1  video/hope/06.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke15111631.mp4 -ss 1 -to 225   -vf scale=480:-1  video/hope/07.mp4
