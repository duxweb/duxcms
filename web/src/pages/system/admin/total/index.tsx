import { ReactNode, useEffect, useMemo, useState } from 'react'
import { useCustom, useCustomMutation, useTranslate } from '@refinedev/core'
import { Card, Statistic, Table, Button, Link } from 'tdesign-react/esm'
import { Main, ChartMap, Modal, useClient } from '@duxweb/dux-refine'
import { useWebSocket, useInterval } from 'ahooks'

const Index = () => {
  const { request } = useClient()
  const translate = useTranslate()

  const { data } = useCustom({
    url: 'system/total',
    method: 'get',
    meta: {
      params: {},
    },
  })
  const info = data?.data

  const [hardware, setHardware] = useState<Record<string, any>>({})
  const [hardwareLoading, setHardwareLoading] = useState<boolean>(true)

  useInterval(() => {
    request(`system/total/hardware`, 'get').then((res) => {
      setHardwareLoading(false)
      setHardware(res.data || [])
    })
  }, 5000)

  const [taskId, setTaskId] = useState<string>('')
  const [speedCconnect, setSpeedCconnect] = useState<boolean>(false)
  const [loading, setLoading] = useState<boolean>(false)
  const [speedData, setSpeedData] = useState<Record<string, any>[]>([])
  const [speedNode, setSpeedNode] = useState<Record<string, any>[]>([])
  const { mutate } = useCustomMutation()

  const { readyState, sendMessage, latestMessage, disconnect, connect } = useWebSocket(
    'wss://data.cesu.net/v1/ws',
    {
      manual: true,
      reconnectLimit: 0,
    },
  )

  useEffect(() => {
    if (taskId) {
      if (speedCconnect) {
        disconnect()
      }
      setTimeout(() => {
        setSpeedData([])
        connect()
      }, 500)
    } else {
      disconnect()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [taskId])

  useEffect(() => {
    if (!taskId) {
      return
    }
    if (readyState == 1) {
      setSpeedCconnect(true)
      sendMessage(
        JSON.stringify({
          taskId: taskId,
        }),
      )
    }
    if (readyState == 3) {
      setSpeedCconnect(false)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [readyState, taskId])

  useEffect(() => {
    if (!latestMessage) {
      return
    }

    setLoading(false)

    const data = JSON.parse(latestMessage?.data)

    if (data?.nid == 0) {
      return
    }

    if (data?.nid != 0 && (data?.code == 200 || data?.code == 500 || data?.code == 400)) {
      setSpeedData((v) => {
        return [
          ...v,
          {
            nid: data?.nid,
            time:
              data?.data?.success && data?.data?.allTime ? Number(data.data.allTime) : undefined,
            info: speedNode?.find((v) => {
              return v?.nid == data?.nid
            }),
          },
        ]
      })
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [latestMessage, speedNode])

  const mapData = useMemo<Record<string, any>[]>(() => {
    return (speedData
      ?.map((item) => {
        if (!item?.info?.china_province) {
          return
        }
        return {
          name: item?.info?.province,
          value: item?.time ? item?.time.toFixed?.(2) : 1000,
        }
      })
      .filter((v) => !!v) || []) as Record<string, any>[]
  }, [speedData])

  const onSpeedTest = (net: string[], token: string) => {
    setLoading(true)
    mutate(
      {
        url: 'system/total/speed',
        method: 'post',
        values: {
          net: net,
          token: token,
        },
      },
      {
        onSuccess(data) {
          setTaskId(data.data?.taskId)
          setSpeedNode(data.data?.nodes)
        },
        onError() {
          setLoading(false)
        },
      },
    )
  }

  return (
    <Main>
      <div className='flex flex-col gap-4'>
        <Card
          title={
            <div className='flex items-center gap-2'>{translate('system.total.general.title')}</div>
          }
          headerBordered
        >
          <div className='grid grid-cols-1 gap-4 2xl:grid-cols-4 md:grid-cols-2'>
            <CardStats
              isLoading={hardwareLoading}
              title={translate('system.total.general.cpuLoad')}
              value={Number(hardware?.load?.toFixed(2))}
              unit='%'
            />
            <CardStats
              isLoading={hardwareLoading}
              title={translate('system.total.general.cpuUse')}
              value={Number(hardware?.cpu)}
              unit='%'
            />
            <CardStats
              isLoading={hardwareLoading}
              title={translate('system.total.general.memUse')}
              value={Number(hardware?.mem)}
              unit='%'
            />
            <CardStats
              isLoading={hardwareLoading}
              title={translate('system.total.general.diskUse')}
              value={hardware?.disk || 0}
              unit='%'
            />
          </div>
        </Card>

        <Card title={translate('system.total.env.title')} headerBordered>
          <div className='row-4 grid grid-cols-1 w-full gap-4 gap-4 2xl:grid-cols-4 md:grid-cols-2'>
            <CardEnv
              title={translate('system.total.env.php')}
              value={info?.sys?.php || '-'}
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-brand-2 p-3 text-brand'>
                  <div className='i-tabler:brand-php size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.mysql')}
              value={info?.sys?.mysql || '-'}
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-success-2 p-3 text-success'>
                  <div className='i-tabler:brand-mysql size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.redis')}
              value={info?.sys?.redis || '-'}
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-error-2 p-3 text-error'>
                  <div className='i-tabler:database size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.time')}
              value={'UTC ' + info?.sys?.time}
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-warning-2 p-3 text-warning'>
                  <div className='i-tabler:calendar-time size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.gdExt')}
              value={
                info?.extend?.gd
                  ? translate('system.total.env.on')
                  : translate('system.total.env.off')
              }
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-pink-1 p-3 text-pink-6 dark:bg-pink-9/50 dark:text-pink-7'>
                  <div className='i-tabler:photo size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.imagickExt')}
              value={
                info?.syextends?.imagick
                  ? translate('system.total.env.on')
                  : translate('system.total.env.off')
              }
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-purple-1 p-3 p-3 text-purple-6 dark:bg-purple-9/50 dark:text-purple-7'>
                  <div className='i-tabler:photo size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.redisExt')}
              value={
                info?.syextends?.redis
                  ? translate('system.total.env.on')
                  : translate('system.total.env.off')
              }
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-teal-1 p-3 p-3 text-teal-6 dark:bg-teal-9/50 dark:text-teal-7'>
                  <div className='i-tabler:database size-8'></div>
                </div>
              }
            />
            <CardEnv
              title={translate('system.total.env.zipExt')}
              value={
                info?.syextends?.zip
                  ? translate('system.total.env.on')
                  : translate('system.total.env.off')
              }
              icon={
                <div className='size-12 flex flex-none items-center justify-center rounded-full bg-yellow-2 p-3 p-3 text-yellow-6 dark:bg-yellow-9/50 dark:text-yellow-7'>
                  <div className='i-tabler:zip size-8'></div>
                </div>
              }
            />
          </div>
        </Card>

        <div className='grid grid-cols-2 gap-4'>
          <Card
            title={translate('system.total.speed.title')}
            description={
              <>
                {translate('system.total.speed.desc')}
                <Link href='https://www.cesu.net/' theme='primary' target='_blank'>
                  {translate('system.total.speed.view')}
                </Link>
              </>
            }
            headerBordered
            actions={
              <div>
                <Button
                  variant='outline'
                  onClick={() => {
                    Modal.open({
                      title: '站点测速',
                      component: () => import('./speed'),
                      componentProps: {
                        onSpeedTest: onSpeedTest,
                      },
                    })
                  }}
                  loading={loading}
                >
                  {translate('system.total.speed.start')}
                </Button>
              </div>
            }
          >
            <div className='h-100 w-full'>
              <ChartMap
                name={translate('system.total.speed.speed')}
                params={{
                  min: 0,
                  max: 10,
                }}
                options={{
                  tooltip: {
                    trigger: 'item',
                    formatter: ({ seriesName, value }: Record<string, any>) => {
                      return `${seriesName} ${value < 1000 ? value?.toFixed?.(2) + 's' : translate('system.total.speed.timeout')}`
                    },
                  },
                }}
                data={mapData}
                map='china'
              />
            </div>
          </Card>
          <Card title={translate('system.total.area.title')} headerBordered>
            <Table
              data={speedData}
              rowKey='nid'
              maxHeight={420}
              columns={[
                {
                  title: translate('system.total.area.area'),
                  colKey: 'province',
                  cell: ({ row }) => {
                    return `${row?.info?.province || ''}${row?.info?.city}`
                  },
                },
                {
                  title: translate('system.total.area.line'),
                  colKey: 'type',
                  cell: ({ row }) => {
                    return row?.info?.type
                  },
                },
                {
                  title: translate('system.total.area.time'),
                  colKey: 'time',
                  sortType: 'all',
                  width: 200,
                  sorter: true,
                  cell: ({ row }) => {
                    return row?.time < 1000
                      ? `${row.time.toFixed?.(2)}s`
                      : translate('system.total.speed.timeout')
                  },
                },
              ]}
              onSortChange={(sort: Record<string, any>) => {
                if (!sort || !sort?.sortBy) {
                  setSpeedData([...speedData])
                  return
                }
                const data = speedData
                  .concat()
                  .sort((a, b) =>
                    sort.descending
                      ? b[sort.sortBy] - a[sort.sortBy]
                      : a[sort.sortBy] - b[sort.sortBy],
                  )
                setSpeedData([...data])
              }}
            />
          </Card>
        </div>
      </div>
    </Main>
  )
}

interface CardStatsProps {
  isLoading?: boolean
  value?: number
  rate?: number
  title?: string
  unit?: string
}
const CardStats = ({ isLoading, title, value, unit }: CardStatsProps) => {
  return (
    <div className='flex items-start gap-4'>
      <Statistic
        title={<div>{title}</div>}
        value={value}
        loading={isLoading}
        unit={unit}
      ></Statistic>
    </div>
  )
}

interface CardEnvProps {
  isLoading?: boolean
  value?: ReactNode
  rate?: number
  title?: string
  icon?: ReactNode
}
const CardEnv = ({ isLoading, title, value, icon }: CardEnvProps) => {
  return (
    <div className='flex gap-4 rounded p-2'>
      {icon}
      <div className='flex flex-1 flex-col'>
        <div className='text-placeholder'>{title}</div>
        <div className='text-base font-bold'>{!isLoading ? value : '-'}</div>
      </div>
    </div>
  )
}

export default Index
