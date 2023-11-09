import React, { useCallback, useState } from 'react'
import { useTranslate } from '@refinedev/core'
import {
  PrimaryTableCol,
  Link,
  Popconfirm,
  Tag,
  Tooltip,
  Loading,
  Dialog,
  Button,
  MessagePlugin,
  Input,
} from 'tdesign-react/esm'
import { PageTable, MediaText, useClient, Modal } from '@duxweb/dux-refine'
import { Icon } from 'tdesign-icons-react'
import dayjs from 'dayjs'
import clsx from 'clsx'

export const colorStyle: Record<string, any> = {
  blue: 'bg-blue-7',
  purple: 'bg-pink-7',
  red: 'bg-red-7',
  yellow: 'bg-yellow-7',
  green: 'bg-green-7',
  gray: 'bg-gray-7',
}

const List = () => {
  const translate = useTranslate()
  const [loading, setLoading] = useState(false)
  const [log, setLog] = useState('')
  const client = useClient()
  const [uninstall, setUninstall] = useState('')
  const [password, setPassword] = useState('')

  const update = useCallback((name: string) => {
    client
      .request('cloud/apps/update', 'post', {
        data: {
          name: name,
        },
      })
      .then((res) => {
        if (res.statusCode !== 200) {
          MessagePlugin.error(res.message)
          return
        }
        setLog(res?.data?.content)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'name',
        title: translate('cloud.apps.fields.name'),
        minWidth: 300,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Image
                src={row.icon}
                className={clsx([colorStyle[row.color], 'p-2'])}
              ></MediaText.Image>
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.desc}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'time',
        title: translate('cloud.apps.fields.time'),
        cell: ({ row }) => {
          return <>{dayjs(row.time * 1000).format('YYYY-MM-DD HH:mm')}</>
        },
      },
      {
        colKey: 'update',
        title: translate('cloud.apps.fields.update'),
        cell: ({ row }) => {
          return (
            <>
              {!row?.update ? (
                <Tag variant='outline'>暂无更新</Tag>
              ) : (
                <Popconfirm
                  content={translate('buttons.confirm')}
                  destroyOnClose
                  placement='top'
                  showArrow
                  theme='default'
                  onConfirm={() => {
                    setLoading(true)
                    update(row.name)
                  }}
                >
                  <span>
                    <Tooltip content={dayjs(row.time * 1000).format('YYYY-MM-DD HH:mm')}>
                      <Tag theme='warning' variant='outline' className='cursor-pointer'>
                        有更新
                      </Tag>
                    </Tooltip>
                  </span>
                </Popconfirm>
              )}
            </>
          )
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link theme='primary' href={`https://www.dux.cn/apps/` + row.id} target='_black'>
                {translate('buttons.show')}
              </Link>

              <Link
                theme='danger'
                onClick={() => {
                  setUninstall(row.name)
                }}
              >
                {translate('buttons.delete')}
              </Link>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )
  return (
    <>
      <PageTable
        columns={columns}
        table={{
          rowKey: 'id',
          pagination: {
            disabled: true,
          },
        }}
        title={translate('cloud.apps.name')}
        actionRender={() => (
          <>
            <Modal
              title={translate('cloud.apps.action.login')}
              trigger={
                <Button icon={<Icon name='user' />} variant='outline' theme='primary'>
                  {translate('cloud.apps.action.login')}
                </Button>
              }
              component={() => import('./login')}
            ></Modal>
            <Modal
              title={translate('cloud.apps.action.install')}
              trigger={
                <Button icon={<Icon name='uninstall' />} variant='outline' theme='success'>
                  {translate('cloud.apps.action.install')}
                </Button>
              }
              component={() => import('./install')}
            ></Modal>
          </>
        )}
      />
      <Dialog
        className='app-modal'
        header='日志'
        footer={false}
        visible={!!log}
        destroyOnClose
        onClose={() => {
          setLog('')
          window.location.reload()
        }}
      >
        <div className='p-4'>
          <pre className='overflow-auto rounded-lg p-4 bg-component'>{log}</pre>
        </div>
      </Dialog>

      <Dialog
        className='app-modal'
        header='确认卸载'
        visible={!!uninstall}
        onClose={() => {
          setPassword('')
          setUninstall('')
        }}
        destroyOnClose
        onConfirm={() => {
          console.log(password)
          setLoading(true)
          client
            .request('cloud/apps/delete', 'post', {
              data: {
                name: uninstall,
                password: password,
              },
            })
            .then((res) => {
              if (res.statusCode !== 200) {
                MessagePlugin.error(res.message)
                return
              }
              setUninstall('')
              setLog(res?.data?.content)
            })
            .finally(() => {
              setLoading(false)
            })
        }}
      >
        <div className='p-4'>
          <div className='mb-2 text-error'>卸载该应用不可恢复，需验证用户密码，请谨慎操作</div>
          <Input
            type='password'
            value={password}
            onChange={(value) => {
              setPassword(() => {
                return value
              })
              console.log(value)
            }}
          />
        </div>
      </Dialog>

      <Loading
        loading={loading}
        fullscreen
        preventScrollThrough={true}
        text='处理中，请稍等'
      ></Loading>
    </>
  )
}

export default List
