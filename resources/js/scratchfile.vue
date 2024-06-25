                        <template v-for="(myFile, index) in attachmentFiles.slice(0,4)" :key="index">
                                <div class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative">
                                    <div v-if="index === 3" class="absolute inset-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl">
                                        + {{ attachmentFiles.length - 4 }} more
                                    </div>
                                    <button @click="removeFile(myFile)" class="absolute z-20 right-1 top-1 w-7 h-7 flex items-center justify-center bg-black/30 hover:bg-black/70 text-white rounded-full">
                                        <XMarkIcon class="size-5"/>
                                    </button>
                                    <img v-if="isImage(myFile.file)" :src="myFile.url" alt="" class="object-cover aspect-square">
                                    <template v-else>
                                        <PaperClipIcon class="w-10 h-10 mb-3"/>
                                        <small class="text-center">{{ myFile.file.name }}</small>
                                    </template>
                                </div>
                            </template>
